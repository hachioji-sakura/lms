<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;
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
    $pagenation = '';
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'mode'=>$request->mode,
      'search_word'=>$request->get('search_word'),
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      'list' => $request->get('list'),
      'attributes' => $this->attributes(),
    ];
    if(empty($ret['_line'])) $ret['_line'] = $this->pagenation_line;
    if(empty($ret['_page'])) $ret['_page'] = 0;
    if(empty($user)){
      //ログインしていない
      abort(419);
    }
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
  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $model = $this->model()->where('id',$id)->first()->user;
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
    return view($this->domain.'.page', [
      'item' => $item,
      'comments'=>$comments,
      'milestones'=>$milestones,
      'charge_students'=>$charge_students,
      'use_icons'=> $this->get_image(),
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
    $model = $this->model()->where('id',$id)->first()->user;
    $item = $model->details();
    $item['tags'] = $model->tags();

    $user = $param['user'];
    //コメントデータ取得
    $use_icons = $this->get_image();

    $view = "calendar";
    return view($this->domain.'.'.$view, [
      'item' => $item,
      'use_icons'=>$use_icons,
    ])->with($param);
  }
  public function schedule(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $model = $this->model()->where('id',$id)->first()->user;
    $item = $model->details();
    $item['tags'] = $model->tags();

    $user = $param['user'];
    //コメントデータ取得
    $use_icons = $this->get_image();

    $view = "schedule";
    $request->merge([
      '_sort' => 'start_time',
    ]);
    $calendars = $this->get_schedule($request, $item->user_id);
    $param["_maxpage"] = floor($calendars["count"] / $param['_line']);
    $calendars = $calendars["data"];
    foreach($calendars as $calendar){
      $calendar = $calendar->details();
    }
    $param["calendars"] = $calendars;
    $list_title = '授業予定';
    switch($request->get('list')){
      case "history":
        //授業履歴
        $list_title = '授業履歴';
        break;
      case "confirm":
        //調整中予定
        $list_title = '調整中予定';
        break;
      case "cancel":
        $list_title = '休み';
        break;
    }
    $param['list_title'] = $list_title;
    return view($this->domain.'.'.$view, [
      'item' => $item,
      'use_icons'=>$use_icons,
    ])->with($param);
  }
  private function get_students(Request $request, $teacher_id){
    $students =  ChargeStudent::with('student')->findTeacher($teacher_id)
      ->likeStudentName($request->search_word)
      ->get();
    foreach($students as $student){
      $student['current_calendar_start_time'] = $student->current_calendar()['start_time'];
      if(empty($student['current_calendar_start_time'])){
        //予定があるものを上にあげて、昇順、予定がないもの（null)を後ろにする
        $student['current_calendar_start_time'] = '9999-12-31 23:59:59';
      }
    }
    $students = $students->sortBy('current_calendar_start_time');
    return $students;
  }
  private function get_schedule(Request $request, $user_id){
    $calendars = UserCalendar::findUser($user_id);
    $from_date = '';
    $to_date = '';
    $sort = 'asc';
    //status: new > confirm > fix >rest, presence , absence
    //other status : cancel
    switch($request->get('list')){
      case "history":
        //履歴
        $sort = 'desc';
        $to_date = date('Y-m-d', strtotime("+1 month"));
        break;
      case "confirm":
        //予定調整中
        $to_date = date('Y-m-d', strtotime("+1 month"));
        $calendars = $calendars->findStatuses(['new', 'confirm']);
        break;
      case "cancel":
        //休み予定
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d', strtotime("+1 month"));
        $calendars = $calendars->findStatuses(['cancel', 'rest']);
        break;
      default:
        $from_date = date('Y-m-d');
        $calendars = $calendars->findStatuses(['rest', 'fix', 'presence', 'absence']);
        break;
    }
    $calendars = $calendars->rangeDate($from_date, $to_date);
    $count = $calendars->count();
    $calendars = $calendars->sortStarttime($sort)
      ->pagenation($request->get('_page'), $request->get('_line'))->get();
    return ["data" => $calendars, "count" => $count];
  }

}
