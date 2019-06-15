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
   * 共通パラメータ取得
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null){
    $id = intval($id);
    $user = $this->login_details($request);
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
    $ret['filter'] = [
      'search_week'=>$request->search_week,
      'search_work' => $request->search_work,
      'search_place' => $request->search_place,
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
      $ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
      $lists = ['cancel', 'confirm', 'exchange', 'recent'];
      foreach($lists as $list){
        $calendars = $this->get_schedule(["list" => $list], $ret['item']->user_id);
        $ret[$list.'_count'] = $calendars["count"];
      }
      $asks = $this->get_ask([], $ret['item']->user_id);
      $ret['ask_count'] = $asks["count"];
      $lists = ['lecture_cancel'];
      foreach($lists as $list){
        $asks = $this->get_ask(["list" => $list], $ret['item']->user_id);
        $ret[$list.'_count'] = $asks["count"];
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
    if($request->has('api')){
      $model = $this->model()->where('id',$id)->first();
      if(isset($model)) $model = $model->details();
      return $this->api_response(200, '','', $model);
    }
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
  public function calendar_settings(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $model = $this->model()->where('id',$id)->first()->user;
    $item = $model->details();
    $item['tags'] = $model->tags();

    $user = $param['user'];
    $view = "calendar_settings";
    $param['view'] = $view;
    return view($this->domain.'.'.$view, [
      'calendar_settings' => $item->get_calendar_settings($param['filter']),
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
       $login_user = $this->login_details($request);
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
       'user' => $this->login_details($request),
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
       $param['item'] = $user->first()->details($this->domain);
       if($param['item']->role.'s' != $this->domain){
         abort(403, 'このページの有効期限がきれています');
       }
       $param['access_key'] = $access_key;
     }
     $param['_edit'] = false;
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
        'user' => $this->login_details($request),
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
        $create_user = $res['data']->user->details($this->domain);
        $form['send_to'] = $create_user->role;
        $this->send_mail($email, $this->domain_name.'登録完了', $form, 'text', 'register');
        if (!Auth::attempt(['email' => $email, 'password' => $password]))
        {
          abort(500);
        }
        if($this->domain==="managers"){
          session()->regenerate();
          session()->put('login_role', "manager");
        }
        return $this->save_redirect($res, $param, $this->domain_name.'登録完了しました。', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key'])->first();
      if(!isset($user)){
        abort(403);
      }
      return $this->transaction(function() use ($form, $user){
        $form['create_user_id'] = $user->id;
        $item = $this->model()->where('user_id', $user->id)->first();
        $item->profile_update($form);
        $user->set_password($form['password']);
        $user->update(['status' => 0]);
        return $item;
      }, $this->domain_name.'本登録', __FILE__, __FUNCTION__, __LINE__ );
    }
    protected function to_manager_page(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['item']['name'] = $param['item']->name();
      $fields = [
        'id' => [
          'label' => 'ID',
        ],
        'name' => [
          'label' => '氏名',
        ],
      ];
      $manager = Manager::where('name_last', $param['item']->name_last)->where('name_first', $param['item']->name_first)->first();
      return view('components.page', [
        'action' => 'to_manager',
        'manager' => $manager,
        'fields'=>$fields])
        ->with($param);
    }
    protected function to_manager(Request $request, $id)
    {
      $result = '';
      $form = $request->all();
      $res = $this->api_response(200);
      $access_key = $this->create_token();
      $param = $this->get_param($request, $id);
      $result = '';
      $email = $param['item']['email'];
      $status = intval($param['item']->user->status);
      $message = '事務登録依頼メールを送信しました';
      $already_manager_id = 0;
      if(isset($form['already_manager_id'])) $already_manager_id = $form['already_manager_id'];
      $manager = $param['item']->to_manager($access_key, $already_manager_id);
      if(isset($manager)){
        $title = "事務兼務仮登録受付";
        $this->send_mail($email,
          $title, [
          'user_name' => $param['item']['name'],
          'access_key' => $access_key,
          'send_to' => 'manager',
        ], 'text', 'entry');
      }
      return $this->save_redirect($res, $param, $message);
    }
    public function month_work(Request $request, $id, $target_month=""){
      if(empty($target_month)) $target_month = date('Y-m');
      $param = $this->get_param($request, $id);
      $model = $this->model()->where('id',$id)->first()->user;
      $item = $model->details();
      $item['tags'] = $model->tags();

      $user = $param['user'];
      $view = "month_work";
      $request->merge([
        '_sort' => 'start_time',
      ]);
      $from_date = $target_month.'-01';
      $to_date = date("Y-m-d", strtotime("+1 month ".$from_date));
      $calendars = $this->get_schedule($request->all(), $item->user_id, $from_date, $to_date);
      $param["_maxpage"] = floor($calendars["count"] / $param['_line']);
      $calendars = $calendars["data"];
      $enable_confirm = true; //確認ボタン押せる場合 = true
      $is_checked = true; //すべて確認済みの場合 = true
      foreach($calendars as $calendar){
        if($calendar->is_last_status()===false){
            $enable_confirm = false;
        }
        if($calendar->is_checked()===false){
            $is_checked = false;
        }
        $calendar = $calendar->details();
      }
      if($param['user']->user_id !== $param['item']->user_id){
        $enable_confirm = false;
      }
      $param["calendars"] = $calendars;
      $list_title = date('Y年m月 勤務実績', strtotime($from_date));
      $param['list_title'] = $list_title;
      $param['view'] = $view;
      $param['is_checked'] = $is_checked;
      $param['enable_confirm'] = $enable_confirm;
      $param['target_month'] = $target_month;
      $param['next_month'] = date("Y-m", strtotime("+1 month ".$from_date));;
      $param['prev_month'] = date("Y-m", strtotime("-1 month ".$from_date));;
      return view($this->domain.'.'.$view, [
        'item' => $item,
      ])->with($param);

    }
    public function month_work_confirm(Request $request, $id){
      $param = $this->get_param($request, $id);
      $form = $request->all();
      $res = $this->api_response();
      if($form['checked_at_type']==='fix'){
        //確認済み
        $res = $this->_month_work_confirm($request);
        $message = '月次勤務実績を確認済みにしました';
        $this->_mail('月次勤務実績 確認', $param['user'], $form, 'calendar_month_work');
      }
      else {
        //訂正依頼
        $message = 'カレンダーの訂正依頼を連絡しました';
        $this->_mail('カレンダーの訂正依頼を受け付けました', $param['user'], $form, 'calendar_correction');
      }
      return $this->save_redirect($res, $param, $message);
    }
    public function _mail($title, $user, $form, $template){
      $form['user'] = $user;
      $res = $this->send_mail($user->email,
       $title,
       $form,
       'text',
       $template);
    }
    public function _month_work_confirm(Request $request){
      $form = $request->all();
      try {
        DB::beginTransaction();
        $check_date = date('Y-m-d H:i:s');
        foreach($form['calendar_id'] as $calendar_id){
          UserCalendar::where('id', $calendar_id)->first()->checked($check_date);
        }
        DB::commit();
        return $this->api_response(200, '', '');
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
     * 生徒取得
     *
     * @param  array  $param
     * @return array
     */
    public function get_charge_students(Request $request , $id){
      $param = $this->get_param($request, $id);
      $items = $param['item']->get_charge_students();

      return $this->api_response(200, "", "", $items);
    }

}
