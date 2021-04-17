<?php

namespace App\Http\Controllers;

use App\Domain\School\Repository\HighSchoolEntityRepository;
use App\Domain\School\SchoolViewEntity;
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
     * @var \App\Domain\School\Repository\HighSchoolEntityRepository
     */
    protected $high_school_entity_repository;
    
    /**
     * SchoolController constructor.
     *
     * @param \App\Domain\School\Repository\HighSchoolEntityRepository $high_school_entity_repository
     */
    public function __construct(
        HighSchoolEntityRepository $high_school_entity_repository
    ) {
        $this->high_school_entity_repository = $high_school_entity_repository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        $search_word = $param['search_word'];
    
        // 対応するページに応じてGetする内容を分岐する
        if (!empty($search_word)) {
            $high_school_entities = $this->high_school_entity_repository->getBySearchWord($search_word);
        } elseif (empty($request->process)) {
            $high_school_entities = $this->high_school_entity_repository->get();
        } else {
            $high_school_entities = $this->high_school_entity_repository->getByProcess($request->process);
        }
        $school_view_entity = new SchoolViewEntity();
        
        // blade側が配列前提のため変換する
        $items = [];
        foreach ($high_school_entities as $high_school_entity) {
            $attribute = $high_school_entity->getAttributes();
            $attribute['id'] = $high_school_entity->highSchoolId();
            $attribute['process'] = $high_school_entity->process();
            $items[] = $attribute;
        }
        $paginator = $this->getPaginator($request, $items);
        
        return view('schools.lists', [
            'items'                => $paginator,
            'fields'               => $school_view_entity->fieldForIndex(),
            'domain'               => $this->domain,
            'is_not_filter_button' => true,
        ])->with($param);
    }
    
    /**
     * 高等学校情報追加ページ
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        
        // 表示情報
        $school_view_entity = new SchoolViewEntity();
        $department_list = $this->high_school_entity_repository->getDepartmentList();
        
        return view('schools.component.form.create_form', [
            'school_view_entity' => $school_view_entity,
            'department_list'    => $department_list,
            'domain'             => $this->domain,
        ])->with($param);
    }
    
    /**
     * 高等学校情報追加
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);

        // 登録処理
        $res = $this->transaction($request, function () use ($request) {
            $name = $request->name;
            $name_kana = $request->name_kana;
            $post_number = $request->post_number ?? '';
            $address = $request->address ?? '';
            $phone_number = $request->phone_number ?? '';
            $fax_number = $request->fax_number ?? '';
            $url = $request->url ?? '';
            $process = $request->process;
            $department_ids = array_map('intval', $request->department_ids);
            $access = $request->access ?? '';
            
            $this->high_school_entity_repository->create(
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
            );
            
            return $this->api_response();
        }, __('labels.create_complete'), __FILE__, __FUNCTION__, __LINE__);
        
        return $this->save_redirect($res, $param, __('labels.create_complete'));
    }
    
    /**
     * ページ詳細
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $high_school_id = $id;
        
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        
        // 表示情報
        $school_view_entity = new SchoolViewEntity();
        $high_school_entity = $this->high_school_entity_repository->findOrFail($high_school_id);
        
        // blade側が配列前提のため変換する
        $attributes = $high_school_entity->getAttributes();
        $attributes['id'] = $high_school_entity->highSchoolId();
        $attributes['process'] = $high_school_entity->process();
        return view('schools.detail', [
            'school_view_entity' => $school_view_entity,
            'item'               => $attributes,
            'fields'             => $school_view_entity->fieldForShow(),
            'domain'             => $this->domain,
            'action'             => $param['action'] ?? null,
        ])->with($param);
    }
    
    /**
     * ページ編集
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        // リクエスト
        $high_school_id = $id;
        
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        
        // 表示情報
        $school_view_entity = new SchoolViewEntity();
        $high_school_entity = $this->high_school_entity_repository->findOrFail($high_school_id);
        $department_list = $this->high_school_entity_repository->getDepartmentList();
        
        return view('schools.component.form.edit_form', [
            'school_view_entity' => $school_view_entity,
            'high_school_entity' => $high_school_entity,
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
        $high_school_id = $id;
        
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        
        // 削除処理
        $res = $this->transaction($request, function () use ($high_school_id) {
            $this->high_school_entity_repository->deleteByHighSchoolId($high_school_id);
            
            return $this->api_response();
        }, __('labels.delete_complete'), __FILE__, __FUNCTION__, __LINE__);
        
        return $this->save_redirect($res, $param, __('labels.delete_complete'));
    }
    
    /**
     * 指定のデータを変更する
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        // リクエスト
        $high_school_id = $id;
        
        // 基盤として最低限必要な要素を用意
        $param = $this->get_common_param($request);
        
        // 更新処理
        $res = $this->transaction($request, function () use ($high_school_id, $request) {
            $high_school_entity = $this->high_school_entity_repository->findOrFail($high_school_id);
            $high_school_entity->changeName((string)$request->name);
            $high_school_entity->changeNameKana((string)$request->name_kana);
            $high_school_entity->changePostNumber((string)$request->post_number);
            $high_school_entity->changeAddress((string)$request->address);
            $high_school_entity->changePhoneNumber((string)$request->phone_number);
            $high_school_entity->changeFaxNumber((string)$request->fax_number);
            $high_school_entity->changeURL((string)$request->url);
            $high_school_entity->changeProcess((array)$request->process);
            $department_ids = array_map('intval', $request->department_ids);
            $high_school_entity->changeDepartment($department_ids);
            $high_school_entity->changeAccess((string)$request->access);
            $this->high_school_entity_repository->save($high_school_entity);
            
            return $this->api_response();
        }, __('labels.update_complete'), __FILE__, __FUNCTION__, __LINE__);
        
        return $this->save_redirect($res, $param, __('labels.update_complete'));
    }
    
    /**
     * ページネーションを作成
     *
     * Modelによるページネーションは負荷的に懸念があるため、Entity用のものを生成する
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Domain\School\HighSchoolEntity[] $high_school_entities
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function getPaginator(Request $request, array $items): LengthAwarePaginator
    {
        $page_number = $request->page ?? 1;
        $per_page = 25;
        $page_slice = collect($items)->forPage($page_number, $per_page);
        
        return new LengthAwarePaginator($page_slice, count($items), $per_page, $page_number, ['path' => $request->url()]);
    }
}
