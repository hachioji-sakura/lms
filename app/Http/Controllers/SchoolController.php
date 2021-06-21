<?php

namespace App\Http\Controllers;

use App\Domain\School\SchoolViewEntity;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolDepartment;
use App\Models\SchoolDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class SchoolController
 *
 * @package App\Http\Controllers
 */
class SchoolController extends MilestoneController
{
    /**
     * @var string
     */
    public $domain = 'schools';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        $page_number = $request->page ?? 1;
        $per_page = 25;
        $search_word = $param['search_word'];

        // 対応するページに応じてGetする内容を分岐する
        if (! empty($search_word)) {
            $builder_school = new School();
            $schools = $builder_school->newQuery()->where('name', 'Like', "%$search_word%")->get()->all();
            $total_count = count($schools);
        } elseif (empty($request->school_type)) {
            $schools = School::offset(($page_number - 1) * $per_page)->limit($per_page)->get();
            $total_count = School::get()->count();
        } else {
            $schools = School::where('school_type',
                $request->school_type)->offset(($page_number - 1) * $per_page)->limit($per_page)->get();
            $total_count = School::where('school_type', $request->school_type)->get()->count();
        }
        $school_view_entity = new SchoolViewEntity();

        $items = [];
        foreach ($schools as $school) {
            $item = [];
            $item['id'] = $school->id;
            $item['name'] = $school->name;
            $item['address'] = $school->address();
            $item['phone_number'] = $school->phoneNumber();
            $item['school_type'] = $this->getSchoolType($school->school_type);
            $items[] = $item;
        }
        $items = new LengthAwarePaginator($items, $total_count, $per_page, $page_number, ['path' => $request->url()]);

        return view('schools.lists', [
            'items'     => $items,
            'fields'    => $school_view_entity->fieldForIndex(),
            'processes' => [],
            'domain'    => $this->domain,
        ])->with($param);
    }

    private function getSchoolType(string $school_type)
    {
        switch ($school_type) {
            case 'high_school':
                return '高校';
            case 'kindergarten':
                return '幼稚園';
            case 'elementary_school':
                return '小学校';
            case 'junior_high_school':
                return '中学校';
            case 'special_school':
                return '特別学校';
            case 'nursing_school':
                return '看護学校';
        }

        return 'その他';
    }

    /**
     * 学校情報追加ページ
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 表示情報
        $school_view_entity = new SchoolViewEntity();
        $builder_department = new Department();
        $department_list = $builder_department->newQuery()->get()->keyBy('id')->all();
        $department_list = collect($department_list)->pluck('department', 'id')->all();
        $school_types = [
            'kindergarten'       => '幼稚園',
            'elementary_school'  => '小学校',
            'junior_high_school' => '中学校',
            'high_school'        => '高校',
            'special_school'     => '特別学校',
            'nursing_school'     => '看護学校',
        ];

        return view('schools.component.form.create_form', [
            'school_view_entity' => $school_view_entity,
            'department_list'    => $department_list,
            'domain'             => $this->domain,
            'school_types'       => $school_types,
        ])->with($param);
    }

    /**
     * 学校情報追加
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 登録処理
        $res = $this->transaction($request, function () use ($request) {
            $school = new School();
            $school->school_type = $request->school_type;
            $school->name = $request->name;
            $school->name_kana = $request->name_kana;
            $school->url = $request->url ?? '';
            $school->save();

            $school_detail = new SchoolDetail();
            $school_detail->school_id = $school->id;
            $school_detail->post_number = $request->post_number ?? '';
            $school_detail->address = $request->address ?? '';
            $school_detail->phone_number = $request->phone_number ?? '';
            $school_detail->fax_number = $request->fax_number ?? '';
            $school_detail->access = $request->access ?? '';
            $school_detail->full_day_grade = ! empty($request->process) && in_array('fullDayGrade', $request->process);
            $school_detail->full_day_credit = ! empty($request->process) && in_array('fullDayCredit', $request->process);
            $school_detail->part_time_grade_night_only = ! empty($request->process) && in_array('partTimeGradeNightOnly',
                    $request->process);
            $school_detail->part_time_credit = ! empty($request->process) && in_array('partTimeCredit', $request->process);
            $school_detail->part_time_credit_night_only = ! empty($request->process) && in_array('partTimeCreditNightOnly',
                    $request->process);
            $school_detail->online_school = ! empty($request->process) && in_array('onlineSchool', $request->process);
            $school_detail->save();

            // 学科登録
            if ($request->school_type === 'high_school') {
                $department_ids = array_map('intval', $request->department_ids);
                foreach ($department_ids as $department_id) {
                    $school_department = new SchoolDepartment();
                    $school_department->school_type = 'high_school';
                    $school_department->school_type_id = $school->id;
                    $school_department->department_id = $department_id;
                    $school_department->save();
                }
            }

            return $this->api_response();
        }, __('labels.create_complete'), __FILE__, __FUNCTION__, __LINE__);

        return $this->save_redirect($res, $param, __('labels.create_complete'));
    }

    /**
     * ページ詳細
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 表示情報
        $school_view_entity = new SchoolViewEntity();

        // blade側が配列前提のため変換する
        $school = School::find($id);
        $item['id'] = $school->id;
        $item['name'] = $school->name;
        $item['name_kana'] = $school->name_kana ?? '';
        $item['url'] = $school->url ?? '';
        $item['phone_number'] = $school->phoneNumber() ?? '';
        $item['access'] = $school->access() ?? '';
        $item['post_number'] = $school->postNumber() ?? '';
        $item['fax_number'] = $school->faxNumber() ?? '';
        $item['address'] = $school->address() ?? '';
        $item['process'] = $school->process() ?? '';
        $item['department_names'] = $school->departmentNames() ?? '';
        $item['school_type'] = $this->getSchoolType($school->school_type);

        return view('schools.detail', [
            'school_view_entity' => $school_view_entity,
            'item'               => $item,
            'fields'             => $school_view_entity->fieldForShow(),
            'domain'             => $this->domain,
            'action'             => $param['action'] ?? null,
        ])->with($param);
    }

    /**
     * ページ編集
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 表示情報
        $school_view_entity = new SchoolViewEntity();

        $school = School::find($id);
        $item['id'] = $school->id;
        $item['name'] = $school->name;
        $item['name_kana'] = $school->name_kana ?? '';
        $item['url'] = $school->url ?? '';
        $item['phone_number'] = $school->phoneNumber() ?? '';
        $item['access'] = $school->access() ?? '';
        $item['post_number'] = $school->postNumber() ?? '';
        $item['fax_number'] = $school->faxNumber() ?? '';
        $item['address'] = $school->address() ?? '';
        $item['process'] = $school->process() ?? '';
        $item['department_names'] = $school->departmentNames() ?? '';
        $item['school_type'] = $this->getSchoolType($school->school_type);
        $item['fullDayGrade'] = $school->fullDayGrade();
        $item['fullDayCredit'] = $school->fullDayCredit();
        $item['partTimeGradeNightOnly'] = $school->partTimeGradeNightOnly();
        $item['partTimeCredit'] = $school->partTimeCredit();
        $item['partTimeCreditNightOnly'] = $school->partTimeCreditNightOnly();
        $item['onlineSchool'] = $school->onlineSchool();
        $item['department_ids'] = $school->departmentIds();
        $builder_department = new Department();
        $department_list = $builder_department->newQuery()->get()->keyBy('id')->all();
        $department_list = collect($department_list)->pluck('department', 'id')->all();

        return view('schools.component.form.edit_form', [
            'school_view_entity' => $school_view_entity,
            'item'               => $item,
            'department_list'    => $department_list,
            'domain'             => $this->domain,
        ])->with($param);
    }

    /**
     * 指定のデータを削除する
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id)
    {
        // リクエスト
        $school_id = $id;

        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 削除処理
        $res = $this->transaction($request, function () use ($school_id) {
            $school_model = new School();
            $school = $school_model->newQuery()->where('id', $school_id)->first();
            if ($school->school_type !== 'high_school') {
                School::destroy($school_id);
                SchoolDetail::destroy($school_id);
            } else {
                School::destroy($school_id);
                SchoolDetail::destroy($school_id);
                SchoolDepartment::where('school_type_id', $school_id)->delete();
            }

            return $this->api_response();
        }, __('labels.delete_complete'), __FILE__, __FUNCTION__, __LINE__);

        return $this->save_redirect($res, $param, __('labels.delete_complete'));
    }

    /**
     * 指定のデータを変更する
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 更新処理
        $res = $this->transaction($request, function () use ($id, $request) {
            $school = School::where('id', $id)->first();
            $school->name = $request->name;
            $school->name_kana = $request->name_kana;
            $school->url = $request->url ?? '';
            $school->save();

            $school_detail = SchoolDetail::where('id', $id)->first();
            $school_detail->post_number = $request->post_number ?? '';
            $school_detail->address = $request->address ?? '';
            $school_detail->phone_number = $request->phone_number ?? '';
            $school_detail->fax_number = $request->fax_number ?? '';
            $school_detail->access = $request->access ?? '';
            $school_detail->full_day_grade = ! empty($request->process) && in_array('fullDayGrade', $request->process);
            $school_detail->full_day_credit = ! empty($request->process) && in_array('fullDayCredit', $request->process);
            $school_detail->part_time_grade_night_only = ! empty($request->process) && in_array('partTimeGradeNightOnly',
                    $request->process);
            $school_detail->part_time_credit = ! empty($request->process) && in_array('partTimeCredit', $request->process);
            $school_detail->part_time_credit_night_only = ! empty($request->process) && in_array('partTimeCreditNightOnly',
                    $request->process);
            $school_detail->online_school = ! empty($request->process) && in_array('onlineSchool', $request->process);
            $school_detail->save();

            // 学科登録
            $builder_high_school_department = new SchoolDepartment();
            $builder_high_school_department->newQuery()->where('school_type_id', $id)->delete();
            if (!empty($request->department_ids)) {
                $department_ids = array_map('intval', $request->department_ids);
                foreach ($department_ids as $department_id) {
                    $school_department = new SchoolDepartment();
                    $school_department->school_type = 'high_school';
                    $school_department->school_type_id = $school->id;
                    $school_department->department_id = $department_id;
                    $school_department->save();
                }
            }

            return $this->api_response();
        }, __('labels.update_complete'), __FILE__, __FUNCTION__, __LINE__);

        return $this->save_redirect($res, $param, __('labels.update_complete'));
    }
}
