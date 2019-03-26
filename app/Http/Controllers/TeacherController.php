<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
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
      '_status' => $request->get('status'),
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
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if(!$this->is_manager($user->role) && $id!==$user->id){
        //講師は自分のidしか閲覧できない
        abort(403);
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
    $user = $param['user'];
    //コメントデータ取得
    $comments = $param['item']->user->target_comments;
    $comments = $comments->sortByDesc('created_at');

    foreach($comments as $comment){
      $create_user = $comment->create_user->details();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_icon = $create_user->icon;
    }
    $view = "page";

    $param['view'] = $view;
    return view($this->domain.'.'.$view, [
      'comments'=>$comments,
      'charge_students'=>$this->get_students($request, $id),
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

    $view = "calendar";
    $param['view'] = $view;
    return view($this->domain.'.'.$view, [
      'item' => $item,
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
        $list_title = '授業履歴';
        break;
      case "confirm":
        $list_title = '予定調整中';
        break;
      case "cancel":
        $list_title = '休み・キャンセル';
        break;
    }
    $param['list_title'] = $list_title;
    $param['view'] = $view;
    return view($this->domain.'.'.$view, [
      'item' => $item,
    ])->with($param);
  }
  private function get_students(Request $request, $teacher_id){
    $students =  Student::findChargeStudent($teacher_id)
      ->searchWord($request->search_word)->get();
    $from_date = date('Y-m-d 00:00:00');
    //TODO:暫定で14日先の予定を表示する
    $to_date = date('Y-m-d 23:59:59', strtotime('+14 day'));
    $teacher = Teacher::where('id', $teacher_id)->first();
    $calendars = UserCalendar::rangeDate($from_date,$to_date)
                  ->findUser($teacher->user_id)
                  ->orderBy('start_time')
                  ->get();
    foreach($students as $student){
      foreach($calendars as $calendar){
        if($calendar->is_member($student->user_id)){
          $student['current_calendar_start_time'] = $calendar['start_time'];
          $student['current_calendar'] = $calendar->details();
          break;
        }
      }
      if(empty($student['current_calendar_start_time'])){
        //予定があるものを上にあげて、昇順、予定がないもの（null)を後ろにする
        $student['current_calendar_start_time'] = '9999-12-31 23:59:59';
      }
    }
    $students = $students->sortBy('current_calendar_start_time');
    return $students;
  }
  /**
   * 仮登録ページ
   *
   * @return \Illuminate\Http\Response
   */
  public function entry(Request $request)
  {
    $param = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'attributes' => $this->attributes(),
    ];
    return view($this->domain.'.entry',
      ['sended' => ''])
      ->with($param);
   }
   /**
    * 仮登録処理
    *
    * @return \Illuminate\Http\Response
    */
   public function entry_store(Request $request)
   {
     $param = [
       'domain' => $this->domain,
       'domain_name' => $this->domain_name,
       'attributes' => $this->attributes(),
     ];
     $send_to = "teacher";
     if($this->domain==="managers") $send_to = "manager";

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
         'send_to' => $send_to,
       ], 'text', 'entry');
       $login_user = $this->login_details();
       if(!isset($login_user)){
         return view($this->domain.'.entry',
           ['result' => $result])->with($param);
       }
     }
     return $this->save_redirect($res, [], '仮登録メールを送信しました');
   }
   public function _entry_store(Request $request)
   {
     $form = $request->all();
     try {
       DB::beginTransaction();
       $form["password"] = 'sakusaku';
       $item = null;
       if($this->domain==="teachers") $item = Teacher::entry($form);
       else $item = Manager::entry($form);
       DB::commit();
       return $this->api_response(200, __FUNCTION__, __LINE__, $item);
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
       'domain' => $this->domain,
       'domain_name' => $this->domain_name,
       'user' => $this->login_details(),
       'attributes' => $this->attributes(),
     ];
     if(isset($param['user'])){
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
       if($param['item']->role.'s' != $this->domain){
         abort(404);
       }
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
        $create_user = $res['data']->user->details();
        $form['send_to'] = $create_user->role;
        $this->send_mail($email, $this->domain_name.'登録完了', $form, 'text', 'register');
        if (!Auth::attempt(['email' => $email, 'password' => $password]))
        {
          abort(500);
        }
        return $this->save_redirect($res, $param, $this->domain_name.'登録完了しました。', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key']);
      $user = $user->first();
      DB::beginTransaction();
      $form['create_user_id'] = $user->id;
      $item = $this->model()->where('user_id', $user->id)->first();
      $item->profile_update($form);
      $user->set_password($form['password']);
      $user->update(['status' => 0]);
      DB::commit();
      return $this->api_response(200, __FUNCTION__, __LINE__, $item);
      if($user->count() < 1){
        abort(403);
      }
      try {
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
