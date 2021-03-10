<?php

namespace App\Domain\School\Repository;

use App\Domain\School\HighSchoolEntity;
use App\Models\Department;
use App\Models\HighSchool;
use App\Models\HighSchoolDepartment;
use App\Models\School;
use DB;
use LogicException;

/**
 * Class HighSchoolEntityRepository
 *
 * @package App\Domain\Repository\School
 */
class HighSchoolEntityRepository
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
     * @return \App\Domain\School\HighSchoolEntity[]
     */
    public function getByProcess(string $process): array
    {
        $builder_high_school = new HighSchool();
        $high_schools = $builder_high_school->newQuery()->where($process, true)->get()->all();
        
        return $this->make($high_schools);
    }
    
    /**
     * 学校情報を取得する
     *
     * View側で切り替えやすいように表示する部分以外もまとめて取得する。
     * 高等学校の情報はそれほど多くはないため、全件取得とする。
     *
     * @return \App\Domain\School\HighSchoolEntity[]
     */
    public function get(): array
    {
        $builder_high_school = new HighSchool();
        $high_schools = $builder_high_school->newQuery()->get()->all();
        
        return $this->make($high_schools);
    }
    
    /**
     * 学校情報を取得する（キーワード検索用：学校名の部分一致のみ対応）
     *
     * @param string $search_word
     * @return \App\Domain\School\HighSchoolEntity[]
     */
    public function getBySearchWord(string $search_word): array
    {
        $builder_school = new School();
        $schools = $builder_school->newQuery()->where('name', 'Like', "%$search_word%")->get()->all();
        $school_ids = collect($schools)->pluck('id')->all();
        
        $builder_high_school = new HighSchool();
        $high_schools = $builder_high_school->newQuery()->whereIn('school_id', $school_ids)->get()->all();
        
        return $this->make($high_schools);
    }
    
    /**
     * Entityを生成する
     *
     * @param \App\Models\HighSchool[] $high_schools
     * @return array
     */
    protected function make(array $high_schools): array
    {
        $builder_high_school_department = new HighSchoolDepartment();
        $high_school_departments = $builder_high_school_department->newQuery()->get()->groupBy('high_school_id', true)->all();
        
        $builder_school = new School();
        $schools = $builder_school->newQuery()->get()->keyBy('id')->all();
        
        $high_school_entities = [];
        foreach ($high_schools as $high_school) {
            $department_names = [];
            $department_ids = [];
            $high_school_departments_filtered = $high_school_departments[$high_school->id];
            foreach ($high_school_departments_filtered as $high_school_department) {
                $department = $this->findDepartmentById($high_school_department->department_id);
                $department_ids[] = $department->id;
                $department_names[] = $department->department;
            }
            
            $high_school_entities[$high_school->id] = new HighSchoolEntity(
                collect($high_school)->toArray(),
                collect($schools[$high_school->school_id])->toArray(),
                $department_ids,
                $department_names
            );
        }
        
        return $high_school_entities;
    }
    
    /**
     * 学校情報を取得する
     *
     * @param int $high_school_id 高等学校ID
     * @return \App\Domain\School\HighSchoolEntity|null
     */
    public function find(int $high_school_id): ?HighSchoolEntity
    {
        $builder_high_school = new HighSchool();
        $high_school = $builder_high_school->newQuery()->where('id', $high_school_id)->get()->first();
    
        if (empty($high_school)) {
            return null;
        }
        $high_school_entities = $this->make([$high_school]);
    
        return reset($high_school_entities);
    }
    
    /**
     * 学校情報を取得する（取得失敗時例外発生）
     *
     * @param int $high_school_id 高等学校ID
     * @return \App\Domain\School\HighSchoolEntity
     */
    public function findOrFail(int $high_school_id): HighSchoolEntity
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
     * @param int $high_school_id
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
        $builder_high_school = new HighSchool();
        $builder_high_school->school_id = $builder_school->id;
        $builder_high_school->post_number = $post_number;
        $builder_high_school->address = $address;
        $builder_high_school->phone_number = $phone_number;
        $builder_high_school->fax_number = $fax_number;
        $builder_high_school->access = $access;
        $builder_high_school->full_day_grade = in_array('fullDayGrade', $process, true);
        $builder_high_school->full_day_credit = in_array('fullDayCredit', $process, true);
        $builder_high_school->part_time_grade_night_only = in_array('partTimeGradeNightOnly', $process, true);
        $builder_high_school->part_time_credit = in_array('partTimeCredit', $process, true);
        $builder_high_school->part_time_credit_night_only = in_array('partTimeCreditNightOnly', $process, true);
        $builder_high_school->online_school = in_array('onlineSchool', $process, true);
        $builder_high_school->save();
        
        // 学科
        $high_school_department_attributes_for_insert = [];
        foreach ($department_ids as $department_id) {
            $attributes = [];
            $attributes['high_school_id'] = $builder_high_school->id;
            $attributes['department_id'] = $department_id;
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $high_school_department_attributes_for_insert[] = $attributes;
        }
        DB::table('high_school_departments')->insert($high_school_department_attributes_for_insert);
    }
    
    /**
     * 指定IDの学校情報を削除する
     *
     * @param int $high_school_id
     */
    public function deleteByHighSchoolId(int $high_school_id): void
    {
        // 現時点では学校と高校の関係性は1：1のものしかないため、高等学校の削除に合わせて学校も同時に削除を行う
        $builder_high_school = new HighSchool();
        $high_school = $builder_high_school->newQuery()->where('id', $high_school_id)->get()->first();
        $builder_high_school->newQuery()->where('id', $high_school_id)->delete();
        
        $builder_high_school_department = new HighSchoolDepartment();
        $builder_high_school_department->newQuery()->where('high_school_id', $high_school_id)->delete();
        
        $builder_school = new School();
        $builder_school->newQuery()->where('id', $high_school->school_id)->delete();
    }
    
    /**
     * 学校情報を保存する
     *
     * @param \App\Domain\School\HighSchoolEntity $high_school_entity
     */
    public function save(HighSchoolEntity $high_school_entity): void
    {
        // 高等学校関連
        $builder_high_school = new HighSchool();
        $high_school = $builder_high_school->newQuery()->where('id', $high_school_entity->highSchoolId())->get()->first();
        $high_school->post_number = $high_school_entity->postNumber();
        $high_school->address = $high_school_entity->address();
        $high_school->phone_number = $high_school_entity->phoneNumber();
        $high_school->fax_number = $high_school_entity->faxNumber();
        $high_school->access = $high_school_entity->access();
        $high_school->full_day_grade = $high_school_entity->fullDayGrade();
        $high_school->full_day_credit = $high_school_entity->fullDayCredit();
        $high_school->part_time_grade_night_only = $high_school_entity->partTimeGradeNightOnly();
        $high_school->part_time_credit = $high_school_entity->partTimeCredit();
        $high_school->part_time_credit_night_only = $high_school_entity->partTimeCreditNightOnly();
        $high_school->online_school = $high_school_entity->onlineSchool();
        $high_school->save();
        
        // 学科についてはすべて一旦消す ⇒ 作成するという処理とする（変更があった時のみ処理する）
        if ($high_school_entity->departmentIdsChanged()) {
            $builder_high_school_department = new HighSchoolDepartment();
            $builder_high_school_department->newQuery()->where('high_school_id', $high_school_entity->highSchoolId())->delete();
            
            $high_school_department_attributes_for_insert = [];
            $department_ids = $high_school_entity->departmentIds();
            foreach ($department_ids as $department_id) {
                $attributes = [];
                $attributes['high_school_id'] = $high_school_entity->highSchoolId();
                $attributes['department_id'] = $department_id;
                $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $high_school_department_attributes_for_insert[] = $attributes;
            }
            DB::table('high_school_departments')->insert($high_school_department_attributes_for_insert);
        }
        
        // 学校関連
        $builder_school = new School();
        $school = $builder_school->newQuery()->where('id', $high_school->school_id)->get()->first();
        $school->name = $high_school_entity->name();
        $school->name_kana = $high_school_entity->nameKana();
        $school->url = $high_school_entity->url();
        $school->save();
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
