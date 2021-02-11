<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolGrade;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use DB;

class SchoolGradeController extends MilestoneController
{
  public $domain = 'school_grades';
  public $table = 'school_grades';

  public function model(){
    return SchoolGrade::query();
  }

  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request)
  {
    $param = $this->get_param($request);
    $items = $this->model();
    $user = $this->login_details($request);
    if($this->is_manager_or_teacher($user->role)!==true){
      //生徒の場合は自分自身を対象とする
      $items = $items->mydata($user->user_id);
    }
    $items = $this->_search_scope($request, $items);
    $count = $items->count();
    $items = $items->paginate($param['_line']);

    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'student_id' => [
        'label' => '生徒ID',
      ],
      'student_name' => [
        'label' => '生徒氏名',
      ],
      'grade_name' => [
        'label' => '学年',
      ],
      'semester_name' => [
        'label' => '学期',
      ],
      'title' => [
        'label' => '内容',
        'link' => 'show',
      ],
    ];

    $fields['buttons'] = [
      'label' => '操作',
      'button' => ['edit', 'delete']
    ];

    return ['items' => $items, 'fields' => $fields, 'count' => $count];
  }

  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];

    $form['student_id'] = $request->get('student_id');
    $form['title'] = $request->get('title');
    $form['grade'] = $request->get('grade');
    $form['semester_no'] = $request->get('semester_no');
    $form['remark'] = $request->get('remark');

    //dd($request->all());
    return $form;
  }


  public function update_form(Request $request){
    $form = [];
    //$form['student_id'] = $request->get('student_id');
    $form['title'] = $request->get('title');
    $form['grade'] = $request->get('grade');
    $form['semester_no'] = $request->get('semester_no');
    $form['remark'] = $request->get('remark');

//    dd($request->all());
//    dd($form);
    return $form;
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

    $fields = [
      'id'  => [
        'label' => 'ID',
      ],
      'student_id' => [
        'label' => '生徒ID',
      ],
      'student_name' => [
        'label' => '生徒氏名'
      ],
    ];

    return view('components.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
      ->with($param);
  }

//新規登録時の処理
  public function _store(Request $request)
  {
    //dd($request->all());
    $form = $this->create_form($request);
    if(empty($form['student_id'])) {
      return $this->bad_request('リクエストエラー', '生徒ID='.$form['student_id']);
    }
    $res = $this->save_validate($request);

    if(!$this->is_success_response($res)){
      return $res;
    }
    $item = $this->model();
    foreach($form as $key=>$val){
      $item = $item->where($key, $val);
    }
    $item = $item->first();
    if(isset($item)){
      return $this->error_response('すでに登録済みです');
    }
    $res = $this->transaction($request, function() use ($request, $form){
      $item = $this->model()->create($form);
      if($request->hasFile('upload_file')){
        if ($request->file('upload_file')->isValid([])) {
          $item->file_upload($request->file('upload_file'));
        }
      }
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }

  //更新時の処理
  public function _update(Request $request, $id)
  {
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $res =  $this->transaction($request, function() use ($request, $id){
      $form = $this->update_form($request);
      $item = $this->model()->where('id', $id)->first();
      $is_file_delete = false;
      if($request->has('upload_file_delete') && $request->get('upload_file_delete')==1){
        $is_file_delete = true;
      }
      $file = null;
      if($request->hasFile('upload_file')){
        if ($request->file('upload_file')->isValid([])) {
          $file = $request->file('upload_file');
        }
      }
      $item->change($form, $file, $is_file_delete);
      return $this->api_response(200, '', '', $item);
    }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;

  }


   public function _search_scope(Request $request, $items)
   {
     //ID 検索
     if(isset($request->id)){
       $items = $items->where('id',$request->id);
     }
     //生徒ID検索
     if(isset($request->student_id)){
       $items = $items->where('student_id',$request->student_id);
     }

     return $items;
   }


   //保存時にパラメータをチェック
   public function save_validate(Request $request)
   {
     $form = $request->all();
     if(empty($form['title']) || empty($form['grade']) || empty($form['semester_no'] )){
       return $this->bad_request('リクエストエラー', 'タイトル='.$form['title'].'/学年='.$form['grade'].'/学期='.$form['semester_no']);
     }
     return $this->api_response(200, '', '');
  }
}
