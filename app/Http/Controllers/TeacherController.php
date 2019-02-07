<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Teacher;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;
use Illuminate\Support\Facades\Auth;
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
      $ret['item'] = $this->model()->where('id',$id)->first()->user->details();
    }
    else {
      //id指定がない、かつ、事務以外はNG
      if($this->is_manager($user->role)!==true){
        abort(403);
      }
    }
    return $ret;
  }
  public function _search_scope(Request $request, $items)
  {
    //ID 検索
    if(isset($request->id)){
      $items = $items->where($this->table.'.id',$request->id);
    }

    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }
    //ステータス
    if(isset($request->status)){
      $items = $items->findStatuses($request->status);
    }
    return $items;
  }
  /**
   * 新規登録画面
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $param = $this->get_param($request);

    return view($this->domain.'.create',
      ['error_message' => ''])
      ->with($param);
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
    /*
    $model = $this->model()->where('id',$id)->first()->user;
    $item = $model->details();
    $item['tags'] = $model->tags();
    */
    $user = $param['user'];
    //コメントデータ取得
    $comments = $param['item']->user->target_comments;
    $comments = $comments->sortByDesc('created_at');


    $charge_students = $this->get_students($request, $id);

    foreach($comments as $comment){
      $create_user = $comment->create_user->details();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_icon = $create_user->icon;
    }
    return view($this->domain.'.page', [
      'comments'=>$comments,
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
  /**
   * 体験授業申し込みページ
   *
   * @return \Illuminate\Http\Response
   */
  public function entry(Request $request)
  {
    $param = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
    ];
    return view($this->domain.'.entry',
      ['sended' => ''])
      ->with($param);
   }
   /**
    * 体験授業申し込みページ
    *
    * @return \Illuminate\Http\Response
    */
   public function entry_store(Request $request)
   {
     $result = '';
     $form = $request->all();
     $res = $this->api_response(200);
     $access_key = $this->create_token();
     $request->merge([
       'access_key' => $access_key,
     ]);

     $user = User::where('email', $form['email'])->first();
     $result = '';
     if(!isset($user)){
       $res = $this->_entry_store($request);
       $result = 'success';
     }
     else {
       if($user->status===1){
         //すでにユーザーが仮登録されている場合は、tokenを更新
         $user->update( ['access_key' => $access_key]);
         $result = 'already';
       }
       else {
         //本登録済み
         $res = $this->error_response('このメールアドレスはすでにユーザー登録済みです。');
       }
     }
     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         $this->domain_name.'仮登録完了', [
         'user_name' => $form['name_last'].' '.$form['name_first'],
         'access_key' => $access_key,
         'send_to' => 'teacher',
       ], 'text', 'entry');
       return view($this->domain.'.entry',
         ['result' => $result]);
     }
     else {
       return $this->save_redirect($res, [], '仮登録メールを送信しました');
     }
   }
   public function _entry_store(Request $request)
   {
     $form = $request->all();
     try {
       DB::beginTransaction();
       $form["password"] = 'sakusaku';
       $items = $this->model()->entry($form);
       DB::commit();
       return $this->api_response(200, __FUNCTION__);
     }
     catch (\Illuminate\Database\QueryException $e) {
       DB::rollBack();
       return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
     catch(\Exception $e){
       DB::rollBack();
       return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
   }
   /**
    * 本登録ページ
    *
    * @return \Illuminate\Http\Response
    */
   public function register(Request $request)
   {
     $result = '';
     $param = [
       'user' => $this->login_details(),
       'attributes' => $this->attributes(),
     ];
     if(!empty($param['user'])){
       $param['result'] = 'logout';
       return view($this->domain.'.register',$param);
     }
     else {
       $access_key = $request->get('key');
       if(!$this->is_enable_token($access_key)){
         $param['result'] = 'token_error';
         return view($this->domain.'.register',$param);
       }
       $user = User::where('access_key',$access_key);
       if($user->count() < 1){
         abort(404);
       }
       $param['item'] = $user->first()->details();
       $param['access_key'] = $access_key;
     }
     return view($this->domain.'.register',$param);
    }
    /**
     * 本登録処理
     *
     * @return \Illuminate\Http\Response
     */
    public function register_update(Request $request)
    {
      $param = [
        'user' => $this->login_details(),
        'attributes' => $this->attributes(),
      ];

      $result = "success";
      $email = "";
      $password = "";
      $form = $request->all();
      if(!empty($param['user'])){
        //ログインユーザーがある場合は、操作させない
        abort(403);
      }
      $access_key = $request->access_key;
      if(!$this->is_enable_token($access_key)){
        $result = "token_error";
        return view($this->domain.'.register',
          ['result' => $result]
        );
      }
      $res = $this->_register_update($request);
      $email = $form['email'];
      $password = $form['password'];

      if($this->is_success_response($res)){
        if(empty($param['user'])){
          $form['send_to'] = 'teacher';
          $this->send_mail($email, '講師登録完了', $form, 'text', 'register');
          if (!Auth::attempt(['email' => $email, 'password' => $password]))
          {
            abort(500);
          }
        }
        return $this->save_redirect($res, $param, '講師登録完了しました。', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key']);
      if($user->count() < 1){
        abort(403);
      }
      try {
        $user = $user->first();
        DB::beginTransaction();
        $form['create_user_id'] = $user->id;
        $teacher = $this->model()->where('user_id', $user->id)->first();
        $teacher->profile_update($form);
        $user->set_password($form['password']);
        $user->update(['status' => 0]);
        DB::commit();
        return $this->api_response(200, __FUNCTION__);
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
    }

}
