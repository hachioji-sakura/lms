<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use App\Models\UserCalendar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App;
/*
*/
use DB;
class TeacherController extends StudentController
{
  public $domain = "teachers";
  public $table = "teachers";

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
    $ret = $this->get_common_param($request);
    $user = $ret['user'];
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if(!$this->is_manager($user->role) && $id!=$user->id){
        //講師は自分のidしか閲覧できない
        abort(403);
      }
      $ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
      $lists = ['cancel', 'confirm', 'exchange', 'today', 'rest_contact'];
      foreach($lists as $list){
        $count = $this->get_schedule(["list" => $list, 'user_id'=>$ret['item']->user_id] , true);
        $ret[$list.'_count'] = $count;
      }
      $asks = $this->get_ask(['user_id' => $ret['item']->user_id], true);
      $ret['ask_count'] = $count;
      $lists = ['lecture_cancel', 'rest_cancel', 'teacher_change'];
      foreach($lists as $list){
        $count = $this->get_ask(["list" => $list, 'user_id'=>$ret['item']->user_id], true);
        $ret[$list.'_count'] = $count;
      }
      $lists = ['confirm_list', 'fix_list'];
      foreach($lists as $list){
        $count = $this->get_user_calendar_settings(["list" => $list, 'user_id'=>$ret['item']->user_id], true);
        $ret[$list.'_setting_count'] = $count;
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
    $view = "home";
    if($request->has('view')){
      switch ($request->get('view')){
        case "setting_menu":
          $view = $request->get('view');
          break;
      }
    }
    $param['view'] = $view;
    return view($this->domain.'.page.'.$view, [
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
  private function get_students(Request $request, $teacher_id){

    $students =  Student::findChargeStudent($teacher_id)
      ->searchWord($request->search_word)->get();

    $from_date = date('Y-m-d 00:00:00');
    //TODO:暫定で14日先の予定を表示する
    $to_date = date('Y-m-d 23:59:59', strtotime('+14 day'));
    $teacher = Teacher::where('id', $teacher_id)->first();
    if(isset($teacher) && isset($students)){
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
    return null;
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
      'domain_name' => __('labels.'.$this->domain),
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
       'domain_name' => __('labels.'.$this->domain),
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
         '仮登録完了', [
         'user_name' => $form['name_last'].' '.$form['name_first'],
         'access_key' => $access_key,
         'send_to' => $send_to,
       ], 'text', 'entry',$form['locale']);
       $login_user = $this->login_details($request);
       if(!isset($login_user)){
         return view($this->domain.'.entry',
           ['result' => $result])->with($param);
       }
     }
     return $this->save_redirect($res, [], '仮登録メールを送信しました。');
   }
   public function _entry_store(Request $request)
   {
     return $this->transaction($request, function() use ($request){
       $form = $request->all();
       $form["password"] = 'sakusaku';
       $item = null;
       if($this->domain==="teachers") $item = Teacher::entry($form);
       else $item = Manager::entry($form);
       return $this->api_response(200, '', '', $item);
     }, __('labels.'.$this->domain).'登録', __FILE__, __FUNCTION__, __LINE__ );
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
       'domain_name' => __('labels.'.$this->domain),
       'user' => $this->login_details($request),
       'attributes' => $this->attributes(),
     ];
     if(isset($param['user'])){
       $param['result'] = 'logout';
       App::setLocale($param['user']->locale);
       return view($this->domain.'.register',$param);
     }
     else {
       $access_key = $request->get('key');
       if(!$this->is_enable_token($access_key)){
         $param['result'] = 'token_error';
         if(isset($param['user'])){
           App::setLocale($param['user']->locale);
         }
         return view($this->domain.'.register',$param);
       }
       $user = User::where('access_key',$access_key);
       if($user->count() < 1){
         abort(404);
       }
       $param['item'] = $user->first()->details($this->domain);
       App::setLocale($user->first()->locale);
       session()->regenerate();
       session()->put('locale', $user->first()->locale);
       $domain = $this->domain;
       if($param['item']->role == 'teacher' && $this->domain!='teachers'){
         abort(403, 'このページの有効期限がきれています');
       }
       if(($param['item']->role == 'staff' || $param['item']->role == 'manager') && $this->domain!='managers'){
         abort(403, 'このページの有効期限がきれています');
       }
       if($param['item']->role == 'staff') $domain = 'managers';

       $param['access_key'] = $access_key;
     }
     $param['_edit'] = true;
     return view($domain.'.register',$param);
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

      if($this->is_success_response($res)){
        $create_user = $res['data']->user->details($this->domain);
        $form['send_to'] = $create_user->role;
        $this->send_mail($create_user->email, '登録完了', $form, 'text', 'register', $res['data']->user->locale);
        Auth::loginUsingId($create_user->user_id);

        if($this->domain==="managers"){
          session()->regenerate();
          session()->put('login_role', "manager");
        }
        return $this->save_redirect($res, $param, '登録完了しました。', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update(Request $request)
    {
      $form = $request->all();

      $user = User::where('access_key',$form['access_key'])->first();
      if(!isset($user)){
        abort(403);
      }
      return $this->transaction(null, function() use ($form, $user){
        $form['create_user_id'] = $user->id;
        $item = $this->model()->where('user_id', $user->id)->first();
        $item->profile_update($form);
        $user->set_password($form['password']);
        $user->update(['status' => 0]);
        return $this->api_response(200, '', '', $item);
      }, '本登録しました。', __FILE__, __FUNCTION__, __LINE__ );
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
          'label' => __('labels.name'),
        ],
      ];
      $manager = Manager::where('name_last', $param['item']->name_last)->where('name_first', $param['item']->name_first)->first();
      $param['action'] = 'to_manager';
      $param['manager'] = $manager;
      $param['fields'] = $fields;
      return view('components.page', [])
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
      $message = '事務登録依頼メールを送信しました。';
      $already_manager_id = 0;
      if(isset($form['already_manager_id'])) $already_manager_id = $form['already_manager_id'];
      $manager = $param['item']->to_manager($access_key, $already_manager_id, $param['user']->user_id);
      if(isset($manager)){
        $title = "事務兼務仮登録受付";
        $this->send_mail($email,
          $title, [
          'user_name' => $param['item']['name'],
          'access_key' => $access_key,
          'send_to' => 'manager',
        ], 'text', 'entry',
        $param['item']->user->locale
      );
      }
      return $this->save_redirect($res, $param, $message);
    }
    public function month_work(Request $request, $id, $target_month=""){
      set_time_limit(600);
      $s = strtotime('now');
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
      $calendars = $this->get_schedule([
        'list' => 'month_work',
        'user_id'=>$item->user_id,
        'search_from_date' => $from_date,
        'search_to_date' => $to_date
      ]);
      $param["_maxpage"] = floor($calendars["count"] / $param['_line']);
      $calendars = $calendars["data"];

      //当月1日より、checked_atに日にちが入っている
      $is_checked = false;
      $check_calendars = [];
      if($calendars!=null){
        $check_calendars = $calendars->where('checked_at','>', $from_date);
        if(count($check_calendars) == count($calendars)){
          $is_checked=true;
        }
      }

      //未入力の予定＝最終ステータス以外
      $enable_confirm = true; //確認ボタン押せる場合 = true
      if($param['user']->user_id !== $param['item']->user_id){
        $enable_confirm = false;
      }
      else {
        $unsolved_calendars = $calendars->whereNotIn('status', ['lecture_cancel', 'cancel', 'rest', 'presence', 'absence'])
                                        ->whereNotIn('work' , [5, 11]);
        if(count($unsolved_calendars) > 0){
          $enable_confirm = false;
        }
      }

      $param["calendars"] = $calendars;

      $param['year'] = date('Y', strtotime($from_date));
      $param['month'] = date('m', strtotime($from_date));
      $param['view'] = $view;
      $param['is_checked'] = $is_checked;
      $param['enable_confirm'] = $enable_confirm;
      $param['target_month'] = $target_month;
      $param['next_month'] = date("Y-m", strtotime("+1 month ".$from_date));
      $param['prev_month'] = date("Y-m", strtotime("-1 month ".$from_date));
      //echo strtotime('now')-$s;
      return view($this->domain.'.'.$view, [
        'item' => $item,
      ])->with($param);

    }
    public function month_work_confirm(Request $request, $id){
      $param = $this->get_param($request, $id);
      $form = $request->all();
      $res = $this->api_response();
      if(isset($form['checked_at_type']) && $form['checked_at_type']==='fix'){
        //確認済み
        $res = $this->_month_work_confirm($request);
        $message = '月次勤務実績を確認済みにしました。';
        $this->_mail('月次勤務実績 確認', $param['user'], $form, 'calendar_month_work');
      }
      else {
        //訂正依頼
        $message = 'カレンダーの訂正依頼を連絡しました。';
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
      return $this->transaction($request, function() use ($request){
        $form = $request->all();
        $check_date = date('Y-m-d H:i:s');
        foreach($form['calendar_id'] as $calendar_id){
          UserCalendar::where('id', $calendar_id)->first()->checked($check_date);
        }
        return $this->api_response(200, '', '', $check_date);
      }, '月次勤怠確定', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 生徒取得
     *
     * @param  array  $param
     * @return array
     */
    public function get_charge_students(Request $request , $id){
      $param = $this->get_param($request, $id);
      if($this->is_manager($param['user']->role)){
        $items = [];
        $students = Student::findStatuses(['regular','trial'])->get();
        foreach($students as $student){
          $detail = $student->user->details("students");
          $detail['grade'] = $detail->tag_value('grade');
          $items[$detail->id] = $detail;
        }
      }
      else {
        $items = $param['item']->get_charge_students();
      }
      return $this->api_response(200, "", "", $items);
    }
    /**
     * 担当生徒追加ページ
     *
     * @param  array  $param
     * @return array
     */
    protected function add_charge_student_page(Request $request , $id){
      $param = $this->get_param($request, $id);
      $charge_students = [];
      $already_students = $param['item']->get_charge_students();
      $ids = [];
      foreach($already_students as $s){
        $ids[] = $s->id;
      }
      $students = Student::whereNotIn('id', $ids)->findStatuses(['regular','trial'])->get();
      $param['_edit'] = false;
      return view('teachers.add_charge_student', [
        'charge_students' => $students,
      ])->with($param);
    }
    protected function add_charge_student(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $res = $this->transaction($request, function() use ($request, $param){
        if(!$request->has('student_id')){
          return $this->bad_request();
        }
        return $param['item']->add_charge_student($request->get('student_id'), $param['user']->user_id);
      }, '担当生徒登録', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, $res['message']);
    }
}
