<?php

namespace App\Domain\School\Repository;

use App\Domain\School\SchoolEntity;
use App\Models\Department;
use App\Models\SchoolDetail;
use App\Models\School;
use App\Models\SchoolDepartment;
use DB;
use LogicException;

/**
 * Class SchoolEntityRepository
 *
 * @package App\Domain\Repository\School
 */
class SchoolEntityRepository
{
    /**
     * @var array
     */
    protected $departments;

    /**
     * 学科リストを取得する
     *
     * @return array
     */
    public function getDepartmentList(): array
    {
        if ($this->departments) {
            return collect($this->departments)->pluck('department', 'id')->all();
        }

        $builder_department = new Department();
        $this->departments = $builder_department->newQuery()->get()->keyBy('id')->all();

        return collect($this->departments)->pluck('department', 'id')->all();
    }

    /**
     * 学校情報を取得する（過程による絞り込み）
     *
     * @param string $process
     * @return \App\Domain\School\SchoolEntity[]
     */
    public function getByProcess(string $process): array
    {
        $builder_school_detail = new SchoolDetail();
        $school_details = $builder_school_detail->newQuery()->where($process, true)->get()->all();

        return $this->make($school_details);
    }

    /**
     * 学校情報を取得する
     *
     * View側で切り替えやすいように表示する部分以外もまとめて取得する。
     * 高等学校の情報はそれほど多くはないため、全件取得とする。
     *
     * @return \App\Domain\School\SchoolEntity[]
     */
    public function get(): array
    {
        $builder_school_detail = new SchoolDetail();
        $school_details = $builder_school_detail->newQuery()->get()->all();

        return $this->make($school_details);
    }

    /**
     * 学校情報を取得する（キーワード検索用：学校名の部分一致のみ対応）
     *
     * @param string $search_word
     * @return \App\Domain\School\SchoolEntity[]
     */
    public function getBySearchWord(string $search_word): array
    {
        $builder_school = new School();
        $schools = $builder_school->newQuery()->where('name', 'Like', "%$search_word%")->get()->all();
        $school_ids = collect($schools)->pluck('id')->all();

        $builder_school_detail = new SchoolDetail();
        $school_details = $builder_school_detail->newQuery()->whereIn('school_id', $school_ids)->get()->all();

        return $this->make($school_details);
    }

    /**
     * 学校情報を取得する
     *
     * @param int $high_school_id 高等学校ID
     * @return \App\Domain\School\SchoolEntity|null
     */
    public function find(int $high_school_id): ?SchoolEntity
    {
        $builder_school_detail = new SchoolDetail();
        $school_detail = $builder_school_detail->newQuery()->where('id', $high_school_id)->get()->first();

        if (empty($school_detail)) {
            return null;
        }
        $high_school_entities = $this->make([$school_detail]);

        return reset($high_school_entities);
    }

    /**
     * 学校情報を取得する（取得失敗時例外発生）
     *
     * @param int $high_school_id 高等学校ID
     * @return \App\Domain\School\SchoolEntity
     */
    public function findOrFail(int $high_school_id): SchoolEntity
    {
        $entity = $this->find($high_school_id);

        if ($entity === null) {
            throw new LogicException('高等学校情報を取得できませんでした。 high_school_id:'.$high_school_id);
        }

        return $entity;
    }

    /**
     * 学校情報を作成する
     *
     * @param $name
     * @param $name_kana
     * @param $post_number
     * @param $address
     * @param $phone_number
     * @param $fax_number
     * @param $url
     * @param $process
     * @param $department_ids
     * @param $access
     */
    public function create(
        $name,
        $name_kana,
        $post_number,
        $address,
        $phone_number,
        $fax_number,
        $url,
        $process,
        $department_ids,
        $access
    ): void {
        // 学校
        $builder_school = new School();
        $builder_school->school_type = 'high_school';
        $builder_school->name = $name;
        $builder_school->name_kana = $name_kana;
        $builder_school->url = $url;
        $builder_school->save();

        // 高等学校情報
        $builder_school_detail = new SchoolDetail();
        $builder_school_detail->school_id = $builder_school->id;
        $builder_school_detail->post_number = $post_number;
        $builder_school_detail->address = $address;
        $builder_school_detail->phone_number = $phone_number;
        $builder_school_detail->fax_number = $fax_number;
        $builder_school_detail->access = $access;
        $builder_school_detail->full_day_grade = in_array('fullDayGrade', $process, true);
        $builder_school_detail->full_day_credit = in_array('fullDayCredit', $process, true);
        $builder_school_detail->part_time_grade_night_only = in_array('partTimeGradeNightOnly', $process, true);
        $builder_school_detail->part_time_credit = in_array('partTimeCredit', $process, true);
        $builder_school_detail->part_time_credit_night_only = in_array('partTimeCreditNightOnly', $process, true);
        $builder_school_detail->online_school = in_array('onlineSchool', $process, true);
        $builder_school_detail->save();

        // 学科
        $school_department_attributes_for_insert = [];
        foreach ($department_ids as $department_id) {
            $attributes = [];
            $attributes['school_type'] = 'high_school';
            $attributes['school_type_id'] = $builder_school_detail->id;
            $attributes['department_id'] = $department_id;
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $school_department_attributes_for_insert[] = $attributes;
        }
        DB::table('school_departments')->insert($school_department_attributes_for_insert);
    }

    /**
     * 指定IDの学校情報を削除する
     *
     * @param int $high_school_id
     */
    public function deleteByHighSchoolId(int $high_school_id): void
    {
        // 現時点では学校と高校の関係性は1：1のものしかないため、高等学校の削除に合わせて学校も同時に削除を行う
        $builder_school_detail = new SchoolDetail();
        $school_detail = $builder_school_detail->newQuery()->where('id', $high_school_id)->get()->first();
        $builder_school_detail->newQuery()->where('id', $high_school_id)->delete();

        $builder_high_school_department = new SchoolDepartment();
        $builder_high_school_department->newQuery()
            ->where('school_type', 'high_school')
            ->where('school_type_id', $high_school_id)
            ->delete();

        $builder_school = new School();
        $builder_school->newQuery()->where('id', $school_detail->school_id)->delete();
    }

    /**
     * 学校情報を保存する
     *
     * @param \App\Domain\School\SchoolEntity $high_school_entity
     */
    public function save(SchoolEntity $high_school_entity): void
    {
        // 高等学校関連
        $builder_school_detail = new SchoolDetail();
        $school_detail = $builder_school_detail->newQuery()->where('id', $high_school_entity->highSchoolId())->get()->first();
        $school_detail->post_number = $high_school_entity->postNumber();
        $school_detail->address = $high_school_entity->address();
        $school_detail->phone_number = $high_school_entity->phoneNumber();
        $school_detail->fax_number = $high_school_entity->faxNumber();
        $school_detail->access = $high_school_entity->access();
        $school_detail->full_day_grade = $high_school_entity->fullDayGrade();
        $school_detail->full_day_credit = $high_school_entity->fullDayCredit();
        $school_detail->part_time_grade_night_only = $high_school_entity->partTimeGradeNightOnly();
        $school_detail->part_time_credit = $high_school_entity->partTimeCredit();
        $school_detail->part_time_credit_night_only = $high_school_entity->partTimeCreditNightOnly();
        $school_detail->online_school = $high_school_entity->onlineSchool();
        $school_detail->save();

        // 学科についてはすべて一旦消す ⇒ 作成するという処理とする（変更があった時のみ処理する）
        if ($high_school_entity->departmentIdsChanged()) {
            $builder_high_school_department = new SchoolDepartment();
            $builder_high_school_department->newQuery()->where('department_id', $high_school_entity->highSchoolId())->delete();

            $school_department_attributes_for_insert = [];
            $department_ids = $high_school_entity->departmentIds();
            foreach ($department_ids as $department_id) {
                $attributes = [];
                $attributes['school_type'] = 'high_school';
                $attributes['school_type_id'] = $high_school_entity->highSchoolId();
                $attributes['department_id'] = $department_id;
                $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $school_department_attributes_for_insert[] = $attributes;
            }
            DB::table('school_departments')->insert($school_department_attributes_for_insert);
        }

        // 学校関連
        $builder_school = new School();
        $school = $builder_school->newQuery()->where('id', $school_detail->school_id)->get()->first();
        $school->name = $high_school_entity->name();
        $school->name_kana = $high_school_entity->nameKana();
        $school->url = $high_school_entity->url();
        $school->save();
    }

    /**
     * Entityを生成する
     *
     * @param  \App\Models\SchoolDetail[]  $school_details
     * @return array
     */
    protected function make(array $school_details): array
    {
        $builder_school_department = new SchoolDepartment();
        $school_departments = $builder_school_department->newQuery()->where('school_type', 'high_school')->get()->groupBy('school_type_id',
            true)->all();

        $builder_school = new School();
        $schools = $builder_school->newQuery()->get()->keyBy('id')->all();

        $high_school_entities = [];
        foreach ($school_details as $school_detail) {
            $department_names = [];
            $department_ids = [];
            $school_departments_filtered = $school_departments[$school_detail->id];
            foreach ($school_departments_filtered as $school_department) {
                $department = $this->findDepartmentById($school_department->department_id);
                $department_ids[] = $department->id;
                $department_names[] = $department->department;
            }

            $high_school_entities[$school_detail->id] = new SchoolEntity(
                collect($school_detail)->toArray(),
                collect($schools[$school_detail->school_id])->toArray(),
                $department_ids,
                $department_names
            );
        }

        return $high_school_entities;
    }

    /**
     * 学科名を取得する
     *
     * @param int $id
     * @return \App\Models\Department
     */
    protected function findDepartmentById(int $id): Department
    {
        if ($this->departments) {
            return $this->departments[$id];
        }

        $builder_department = new Department();
        $this->departments = $builder_department->newQuery()->get()->keyBy('id')->all();

        return $this->departments[$id];
    }
}
