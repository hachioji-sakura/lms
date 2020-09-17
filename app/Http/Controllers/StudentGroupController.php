<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Lecture;
use App\Models\ChargeStudent;
use App\Models\UserTag;
use App\Models\StudentGroup;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use DB;
use View;

class StudentGroupController  extends MilestoneController
{
  public $domain = 'student_groups';
  public $table = 'student_groups';

  public function model(){
    return StudentGroup::query();
  }
  public function show_fields($type){
    $ret = [
      'title' => [
        'label' => 'グループ名',
        'size' => 6,
      ],
      'type_name' => [
        'label' => 'タイプ',
        'size' => 6,
      ],
      'teacher_name' => [
        'label' => '担当講師',
        'size' => 6,
      ],
      'student_name' => [
        'label' => __('labels.students'),
        'size' => 6,
      ],
      'remark' => [
        'label' => '説明',
      ],
    ];
    return $ret;
  }

  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    if(!isset($user)){
      abort(403);
    }
    //$user = User::where('id', 607)->first()->details();
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'remind' => false,
      'token' => $this->create_token(1728000),    //token期限＝20日
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'cancel_reason' => $request->cancel_reason,
      'rest_reason' => $request->rest_reason,
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_type'=>$request->search_type,

    ];
    $ret['is_proxy'] = false;
    if($request->has('is_proxy')){
      $ret['is_proxy'] = true;
    }
    if($request->has('rest_reason')){
      $ret['rest_reason'] = $request->get('rest_reason');
    }
    if($request->has('cancel_reason')){
      $ret['cancel_reason'] = $request->get('cancel_reason');
    }
    if(is_numeric($id) && $id > 0){
      $user_id = -1;
      if($request->has('user')){
        $user_id = $request->get('user');
      }
      $item = $this->model()->where('id',$id)->first();
      if(!isset($item)){
        abort(404, 'ページがみつかりません(1)');
      }
      if($this->is_manager($user->role)!==true && $user->id!=$item->teacher_id){
        abort(403, 'このページにはアクセスできません('.$user->id.'!='.$item->teacher_id.')('.$this->is_manager($user->role).')');
      }

      $ret['item'] = $item->details();
    }

    return $ret;
  }

  public function index(Request $request)
  {
    if(!$request->has('_origin')){
      $request->merge([
        '_origin' => $this->domain,
      ]);
    }
    if(!$request->has('_line')){
      $request->merge([
        '_line' => $this->pagenation_line,
      ]);
    }
    if(!$request->has('_page')){
      $request->merge([
        '_page' => 1,
      ]);
    }
    else if($request->get('_page')==0){
      $request->merge([
        '_page' => 1,
      ]);
    }
    /*
    if(!$request->has('_sort')){
      $request->merge([
        '_sort' => 'start_time',
        '_sort_order' => 'desc',
      ]);
    }
    */
    $param = $this->get_param($request);
    $user = $this->login_details($request);
    if(!isset($user)) abort(403);

    $_table = $this->search($request);
    return view($this->domain.'.lists', $_table)
      ->with($param);
  }
  public function api_index(Request $request, $teacher_id=0)
  {
    $param = $this->get_param($request);
    $user = $this->login_details($request);
    if(!isset($user)) return $this->forbidden();
    if(!($this->is_manager($user->role) || ($this->is_teacher($user->role) && $user->id == $teacher_id))){
      //事務または、講師＝本人でないと生徒グループ取得不可能
     return $this->forbidden();
    }
    $items = $this->model();
    $items = $items->where('teacher_id', $teacher_id);
    $items = $this->_search_scope($request, $items);
    $items = $this->_search_sort($request, $items);
    $items = $items->paginate($param['_line']);
    foreach($items as $item){
      $item = $item->details();
    }
    return $this->api_response(200, "", "", $items->toArray());
  }
  /**
   * フィルタリングロジック
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  Collection $items
   * @return Collection
   */
  public function _search_scope(Request $request, $items)
  {
    //ID 検索
    if(isset($request->id)){
      $items = $items->where('id',$request->id);
    }
    //type 検索
    if(isset($request->search_type) && !empty($request->search_type)){
      $_param = "";
      if(gettype($request->search_type) == "array") $_param  = $request->$search_type;
      else $_param = explode(',', $request->search_type.',');
      $items = $items->whereIn('type',$_param);
    }
    //講師指定
    if(isset($request->teacher_id) && $request->teacher_id>0){
      $items = $items->where('teacher_id',$request->teacher_id);
    }
    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      $items = $items->where(function($items)use($search_words){
        foreach($search_words as $_search_word){
          if(empty($_search_word)) continue;
          $_like = '%'.$_search_word.'%';
          $items->orWhere('title','like',$_like)->orWhere('remark','like',$_like);
        }
      });
    }

    return $items;
  }

  public function search(Request $request)
  {
    $user = $this->login_details($request);
    if(!isset($user)) return $this->forbidden();

    $items = $this->model();
    $items = $this->_search_scope($request, $items);
    $count = $items->count();
    $items = $this->_search_sort($request, $items);
    $items = $items->paginate($request->get('_line'));
    foreach($items as $item){
      $item = $item->details();
    }
    $fields = [
      "id" => [
        "label" => "ID",
      ],
      "title" => [
        "label" => "グループ名",
        "link" => "show",
      ],
      "teacher_name" => [
        "label" => "担当講師",
      ],
      "type_name" => [
        "label" => "タイプ",
      ],
      "student_name" => [
        "label" => __('labels.students'),
      ],
      "buttons" => [
        "label" => __('labels.control'),
        "button" => [
          "edit",
          "delete"]
      ]
    ];
    return ["items" => $items, "fields" => $fields, "count" => $count];
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function create(Request $request)
   {
      $param = $this->get_param($request);
      $param['teachers'] = [];
      if($param['user']->role==="teacher"){
        $param['teachers'][] = $param['user'];
      }
      else if($param['user']->role==="manager"){
        $teachers = Teacher::findStatuses(["unsubscribe"], true);
        if($param['teacher_id'] > 0){
          $teachers = $teachers->where('id', $param['teacher_id']);
        }
        $param['teachers'] = $teachers->get();
      }
      $param['item'] = [];
      return view($this->domain.'.create',
        [ 'error_message' => '', '_edit' => false])
        ->with($param);
    }
    public function teacher_create(Request $request, $teacher_id)
    {
      $request->merge([
        'item_id' => $teacher_id,
        'origin' => 'teachers'
      ]);
      return $this->create($request);
     }
    /**
     * 新規登録
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $param = $this->get_param($request);
      $res = $this->_store($request);

      return $this->save_redirect($res, $param, '追加しました。');
    }
    public function save_validate(Request $request)
    {
      $form = $request->all();
      if(empty($form['title']) || empty($form['teacher_id'])) {
        return $this->bad_request("パラメータ不正");
      }
      if(count($form['student_id']) < 0) {
        return $this->bad_request("グループに生徒の設定が必要です。");
      }
      if($form['type'] == 'family'){
        //生徒が全員家族かどうかチェック
        foreach($form['student_id'] as $student_id1){
          $student1 = Student::where('id', $student_id1)->first();
          foreach($form['student_id'] as $student_id2){
            if($student_id1==$student_id2) continue;
            if($student1->is_family($student_id2)==false){
              return $this->bad_request("ファミリーの場合、家族の生徒を登録してください。");
            }
          }
        }
      }
      return $this->api_response(200, '', '');
    }
    /**
     * 新規登録ロジック
     *
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
      $form = $request->all();
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)) return $res;
      $param = $this->get_param($request);
      $form['create_user_id'] = $param['user']->user_id;
      $res = $this->transaction($request, function() use ($form){
        $item = StudentGroup::add($form);
        return $this->api_response(200, '', '', $item);
      }, '追加しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    /**
     * 削除処理
     *
     * @return \Illuminate\Http\Response
     */
    public function _delete(Request $request, $id)
    {
      $res = $this->transaction($request, function() use ($request, $id){
        $param = $this->get_param($request, $id);
        $user = $this->login_details($request);
        $item = $param["item"];
        $item->dispose();
        return $this->api_response(200, '', '', $item);
      }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']->type);
      return view($this->domain.'.page', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['teachers'][] = $param['item']->teacher;
      $is_all = false;
      if($this->is_manager($param['user']->role)){
        $is_all = true;
      }
      $param['students'] = $param['item']->teacher->get_charge_students($is_all);
      return view($this->domain.'.create', [
        '_edit' => true])
        ->with($param);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $res = $this->_update($request, $id);
      return $this->save_redirect($res, $param, '更新しました。');
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)) return $res;

      $res = $this->transaction($request, function() use ($request,$id){
        $form = $request->all();
        $param = $this->get_param($request, $id);
        $form['create_user_id'] = $param['user']->user_id;
        $item = $this->model()->where('id',$id)->first();
        $item->change($form);
        return $this->api_response(200, '', '', $item);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );

      return $res;
    }

}
