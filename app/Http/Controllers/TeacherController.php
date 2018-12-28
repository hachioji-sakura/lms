<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ChargeStudent;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
/*
*/
use DB;
class TeacherController extends StudentController
{
  public $domain = "teachers";
  public $table = "teachers";
  public $domain_name = "講師";
  public $default_image_id = 3;
  /**
   * このdomainで管理するmodel
   *
   * @return model
   */
  public function model(){
   return Teacher::query();
  }
  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request)
  {
   $param = $this->get_param($request);
   $_table = $this->search($request);
   return view($this->domain.'.tiles', $_table)
    ->with($param);
  }
  /**
   * 共通パラメータ取得
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null){
    $id = intval($id);
    $user = $this->login_details();
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'search_word'=>$request->search_word
    ];
    //講師・事務以外はNG
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if($this->is_teacher($user->role) && $id!==$user->id){
        //講師は自分のidしか閲覧できない
        abort(404);
      }
    }
    else {
      //id指定がない、かつ、事務以外はNG
      if($this->is_manager($user->role)!==true){
        abort(403);
      }
    }
    return $ret;
  }
  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request)
  {
   $items = DB::table($this->table)
    ->join('users', 'users.id',$this->table.'.user_id')
    ->join('images', 'images.id','users.image_id');

   $items = $this->_search_scope($request, $items);

   $items = $this->_search_pagenation($request, $items);

   $items = $this->_search_sort($request, $items);

   $select_raw = <<<EOT
    $this->table.id,
    $this->table.name,
    $this->table.kana,
    images.s3_url as icon
EOT;
   $items = $items->selectRaw($select_raw)->get();
   return ["items" => $items->toArray()];
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
    $items = $items->where($this->table.'.id',$request->id);
   }
   //検索ワード
   if(isset($request->search_word)){
    $search_words = explode(' ', $request->search_word);
    foreach($search_words as $_search_word){
      $_like = '%'.$_search_word.'%';
      $items = $items->where($this->table.'.name','like', $_like)
       ->orWhere($this->table.'.kana','like', $_like);
    }
   }
   //メールアドレス検索
   if(isset($request->email)){
    $_like = '%'.$request->email.'%';
    $items = $items->where('users.email','like', $_like);
   }
   return $items;
  }
  public function _store(Request $request)
  {
   $form = $request->all();
   try {
    DB::beginTransaction();
    $form["image_id"] = $this->default_image_id;
    $res = $this->user_create($form);
    if($this->is_success_response($res)){
      $form['user_id'] = $res["data"]->id;
      $user = $this->login_details();
      $form["create_user_id"] = $user->user_id;
      unset($form['image_id']);
      unset($form['_token']);
      unset($form['password']);
      unset($form['email']);
      unset($form['password-confirm']);
      $_item = $this->model()->create($form);
      DB::commit();
      return $this->api_response(200, "", "", $_item);
    }
    return $res;
   }
   catch (\Illuminate\Database\QueryException $e) {
      DB::rollBack();
      return $this->error_response("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
   }
   catch(\Exception $e){
      DB::rollBack();
      return $this->error_response("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
   }
  }
  private function get_students(Request $request, $id){
    $sql = <<<EOT
    select
     A.id,
     A.name,
     A.kana,
     uc.id as calendar_id,
     uc.start_time as current_schedule,
     lesson.attribute_name as lesson,
     course.attribute_name as course,
     subject.attribute_name as subject
    from (
    select
      s.id as id,
      concat(s.name_last,' ', s.name_first)as name,
      concat(s.kana_last,' ', s.kana_first)as kana,
      (select
        uc.id
       from user_calendars uc
        where uc.start_time > current_timestamp
         and uc.id in (
           select calendar_id from user_calendar_members
           where user_id= s.user_id
         )
        order by uc.start_time
         limit 1
       ) as current_calendar_id
    from students as s
    inner join users u on u.id = s.user_id and u.status < 9
    and s.id in (
      select cs.student_id from charge_students cs
      where teacher_id = ?
     )) as A
     left join user_calendars uc on uc.id = A.current_calendar_id
      left join lectures l on l.id = uc.lecture_id
      left join general_attributes lesson on lesson.attribute_key = 'lesson' and lesson.attribute_value = l.lesson
      left join general_attributes subject on subject.attribute_key = 'subject' and subject.attribute_value = l.subject
      left join general_attributes course on course.attribute_key = 'course' and course.attribute_value = l.course
EOT;
    $where_string = '';
    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      foreach($search_words as $_search_word){
         $_like = "'%".$_search_word."%'";
         $where_string .= " OR A.name like ".$_like;
         $where_string .= " OR A.kana like ".$_like;
      }
      $where_string =' where '.trim($where_string, ' OR');
    }
    $sql .= $where_string;
    $sql .= " order by uc.start_time is null asc, uc.start_time asc";
    $items = DB::select($sql, [$id]);
    return $items;
  }
  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $model = $this->model()->find($id)->user;
    $item = $model->details();
    $item['tags'] = $model->tags();

    $user = $param['user'];
    //コメントデータ取得
    $comments = $model->target_comments;
    $comments = $comments->sortByDesc('created_at');

    //目標データ取得
    $milestones = $model->target_milestones;

    $charge_students = $this->get_students($request, $id);

    foreach($comments as $comment){
      $create_user = $comment->create_user->details();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_icon = $create_user->icon;
    }
    $use_icons = DB::table('images')
      ->where('create_user_id','=',$user->user_id)
      ->orWhere('publiced_at','<=', date('Y-m-d'))
      ->get(['id', 'alias', 's3_url']);
    return view($this->domain.'.page', [
      'item' => $item,
      'comments'=>$comments,
      'milestones'=>$milestones,
      'charge_students'=>$charge_students,
      'use_icons'=>$use_icons,
    ])->with($param);
  }
  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function calendar(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $model = $this->model()->find($id)->user;
    $item = $model->details();
    $item['tags'] = $model->tags();

    $user = $param['user'];
    //コメントデータ取得


    $use_icons = DB::table('images')
      ->where('create_user_id','=',$user->user_id)
      ->orWhere('publiced_at','<=', date('Y-m-d'))
      ->get(['id', 'alias', 's3_url']);
    return view($this->domain.'.calendar', [
      'item' => $item,
      'use_icons'=>$use_icons,
    ])->with($param);
  }
}
