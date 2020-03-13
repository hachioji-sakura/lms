<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarTagSetting;
use App\Models\Traits\Common;
use App\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Tuition;

class UserCalendarMemberSetting extends UserCalendarMember
{
  use Common;
  protected $table = 'lms.user_calendar_member_settings';
  protected $guarded = array('id');
  public $api_endpoint = [
    "GET" =>  "api_get_repeat_schedule.php",
    "PUT" =>  "api_update_repeat_schedule.php",
    "POST" =>  "api_insert_repeat_schedule.php",
    "DELETE" =>  "api_delete_repeat_schedule.php",
  ];

  public static $rules = array(
      'user_id' => 'required',
  );
  public function setting(){
    return $this->belongsTo('App\Models\UserCalendarSetting', 'user_calendar_setting_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function dispose($login_user_id){
    $login_user = User::where('id', $login_user_id)->first();
    if(!isset($login_user)) return false;

    $c = 0;
    foreach($this->setting->members as $member){
      if($member->id == $this->id) continue;
      if($member->user->details('students')->role!='student') continue;
      $c++;
    }
    $this->office_system_api("DELETE");
    if($c===0){
      UserCalendarSetting::where('id', $this->user_calendar_setting_id)->delete();
      UserCalendarTagSetting::where('user_calendar_setting_id', $this->user_calendar_setting_id)->delete();
      UserCalendarMemberSetting::where('user_calendar_setting_id', $this->user_calendar_setting_id)->delete();
    }
    else {
      $this->delete();
    }
  }
  public function office_system_api($method){
    if($this->setting_id_org == 0 && $method=="PUT") return null;;
    if($this->setting_id_org == 0 && $method=="DELETE") return null;;
    if($this->setting_id_org > 0 && $method=="POST") return null;;

    $_url = config('app.management_url').$this->api_domain.'/'.$this->api_endpoint[$method];

    //事務システムのAPIは、GET or POSTなので、urlとともに、methodを合わせる
    $_method = "GET";
    if($method!=="GET") $_method = "POST";
    $student_no = "";
    $teacher_no = "";
    $manager_no = "";
    foreach($this->setting->members as $_member){
      $user = $_member->user->details('students');
      if($user->role==="student"){
        $student_no = $user->get_tag('student_no')["value"];
      }
      $user = $_member->user->details('teachers');
      if($user->role==="teacher"){
        $teacher_no = $user->get_tag('teacher_no')["value"];
      }
      $user = $_member->user->details('managers');
      if($user->role==="manager"){
        $manager_no = $user->get_tag('manager_no')["value"];
      }
    }
    \Log::warning("事務システムAPI student_no=:".$student_no."\nteacher_no=".$teacher_no."\nwork=".$this->setting->work);

    $user = $this->user->details('teachers');
    if(($this->setting->work==6 || $this->setting->work==7 || $this->setting->work==8)
        && $user->role==="teacher" && !empty($student_no)){
      //講師がメンバーかつ、生徒が取得可能な場合　＝　授業予定のカレンダー
      //生徒がメンバーかつ、講師が取得可能時に処理を行うので、APIは無視
      \Log::warning("授業予定の場合、参加者が講師だけではAPIを実行できない");
      return null;
    }

    $lesson = 0;
    $tags =  $this->setting->tags->where('tag_key', 'lesson')->first();
    if(isset($tags)) $lesson = $tags->tag_value;

    $course = 0;
    $tags =  $this->setting->tags->where('tag_key', 'course_type')->first();
    if(isset($tags)) {
      $course = $tags->tag_value;
      $replace_course = config('replace.course');
      //single:1 / group:2 / family:3 に置き換え
      if(isset($replace_course[$course])){
        $course = $replace_course[$course];
      }
    }

    //レクチャー取得できない場合
    //TODO : lesson + courseからlectureを取得(subject=0)
    $lecture_id_org = 0;
    $lecture = Lecture::where('id', $this->lecture_id)->first();
    if(isset($lecture)){
      $lecture_id_org = $lecture->lecture_id_org;
    }
    else {
      //TODO:レクチャが取得できない=lesson ・ courseから取得
      $lecture = Lecture::where('lesson', $lesson)->where('course', $course)->first();
      if(isset($lecture)){
        $lecture_id_org = $lecture->lecture_id_org;
      }
    }

    $__user_id = $student_no;
    switch($this->setting->work){
      case 9:
        //事務のスケジュール
        $__user_id = $manager_no;
        $teacher_no = "";
        $student_no = "";
        break;
    }
    $kind="m";
    if($this->setting->schedule_method=="week") $kind="w";
    $week = ["SU" => "sun", "MO" => "mon", "TU" => "tue", "WE" => "wed", "TH" => "thi", "FR" => "fri", "SA" => "sat"];

    $week_code = "";
    foreach($week as $key=>$val){
      if($val==$this->setting->lesson_week){
        $week_code = $key;
        break;
      }
    }
    $dayofmonth = "";
    $dayofweek = "";
    if($kind=="m"){
      $dayofmonth = $this->setting->lesson_week_count.$week_code;
    }
    else {
      $dayofweek = $week_code;
    }
    $postdata =[];
    $startdate = $this->setting->enable_start_date;
    $enddate = $this->setting->enable_end_date;
    if(empty($startdate)) $startdate = '2000-01-01';
    if(empty($enddate)) $enddate = '2100-01-01';
    switch($method){
      case "PUT":
      case "POST":
        $postdata = [
          "user_id" => $__user_id,
          "student_no" => $student_no,
          "teacher_id" => $teacher_no,
          "kind" => $kind,
          "dayofweek" => $dayofweek,
          "dayofmonth" => $dayofmonth,
          "startdate" => $startdate,
          "enddate" => $enddate,
          "starttime" => $this->setting->from_time_slot,
          "endtime" => $this->setting->to_time_slot,
          "lecture_id" => $lecture_id_org,
          "subject_expr" => implode (',', $this->setting->subject()),
          "work_id" => $this->setting->work,
          "place_id" => $this->setting->place_floor_id,
        ];
        break;
    }
    if($method==="PUT" || $method==="DELETE"){
      $postdata['id'] = $this->setting_id_org;
    }

    $postdata['updateuser'] = 1;
    $message = "";
    foreach($postdata as $key => $val){
      $message .= '['.$key.':'.$val.']';
    }
    $res = $this->call_api($_url, $_method, $postdata);
    $str_res = json_encode($res);
    \Log::info("事務システムAPI Request:".$_url."\n".$message);
    \Log::info("事務システムAPI Response:".$_url."\n".$str_res);
    @$this->send_slack("事務システムAPI Request:".$_url."\n".$message, 'warning', "事務システムAPI");
    @$this->send_slack("事務システムAPI Response:".$_url."\n".$str_res, 'warning', "事務システムAPI");
    if($res["status"] != 0){
      @$this->send_slack("事務システムAPIエラー:".$_url."\nstatus=".$res["status"], 'warning', "事務システムAPIエラー");
    }
    if($method==="POST" && $this->setting_id_org==0){
      //事務システム側のIDを更新
      if(isset($res['id'])){
        $this->update(['setting_id_org'=>$res["id"]]);
        \Log::info("事務システムAPI ID更新:".$res["id"]);
      }
      else{
        @$this->send_slack("事務システムAPIエラー:IDがとれない", 'warning', "事務システムAPIエラー");
      }
    }
    return $res;
  }
  public function get_tuition(){
    if($this->setting->is_teaching()!=true){
      return null;
    }
    $user = $this->user->details();
    if($user->role!='student'){
      return null;
    }
    $lesson = $this->setting->lesson(true);
    $course = $this->setting->course(true);
    $grade = $user->tag_value('grade');
    $course_minutes = $this->setting->course_minutes(true);
    //体験の場合、まだis_enable=trueの状況で、feeを確定することになる
    $filter = ["search_status" => ["new", "confirm", "fix"]];
    $settings = $user->get_calendar_settings($filter);
    $lesson_week_count = 0;
    foreach($settings as $setting){
      if($lesson==$setting->lesson(true) &&
        $course == $setting->course(true)){
          if($lesson_week_count < 3) $lesson_week_count++;
      }
    }
    $setting_details = $this->setting->details(1);
    $teacher = $this->setting->user->details('teachers');
    $subject = '';
    if($this->setting->get_tag_value('lesson')==2 && $this->setting->has_tag('english_talk_lesson', 'chinese')==true){
      $subject= $this->setting->get_tag_value('subject');
    }
    elseif($this->setting->get_tag_value('lesson')==4){
      $subject= $this->setting->get_tag_value('kids_lesson');
    }
    $tuition = Tuition::where('student_id', $user->id)
                ->where('teacher_id', $teacher->id)
                ->where('lesson', $lesson)
                ->where('course_type', $course)
                ->where('course_minutes', $course_minutes)
                ->where('grade', $grade)
                ->where('lesson_week_count', $lesson_week_count)
                ->where('subject', $subject)->first();
    if(!isset($tuition)) return null;
    return $tuition;
  }
  static public function get_api_lesson_fee($lesson, $course, $course_minutes, $lesson_week_count, $grade, $is_juken=false, $teacher_id){
    $replace_course = config('replace.course');
    if(!isset($replace_course[$course])){
      return null;
    }
    $replace_course = $replace_course[$course];

    $replace_grade = config('replace.grade');
    if(!isset($replace_grade[$grade])){
      return null;
    }
    $replace_grade = $replace_grade[$grade];

    $jukensei_flag = 0;
    if($is_juken==true){
      $jukensei_flag = 1;
    }

    $_url = config('app.management_url').'/sakura-api/api_get_lesson_fee.php';
    $_url .='?lesson='.$lesson;
    $_url .='&course_type='.$replace_course;
    $_url .='&course_minutes='.$course_minutes;
    $_url .='&grade='.$replace_grade;
    $_url .='&jukensei_flag='.$jukensei_flag;
    $_url .='&lesson_week_count='.$lesson_week_count;
    $controller = new Controller;
    $request = new Request;
    $res = $controller->call_api($request, $_url, 'GET', null);

    $message = "";
    $message = "事務システムAPI \nRequest:".$_url."\n".$message;
    if(empty($res)){
      $message .= "\n事務システムAPIエラー:".$_url."\nresponseなし";
      \Log::Error($message);
      @$controller->send_slack($message, 'error', "事務システムAPI");
      return null;
    }
    else if($res["status"] != 'success'){
      $message .= "\n事務システムAPIエラー:".$_url."\nstatus=".$res["status"];
      \Log::Error($message);
      @$controller->send_slack($message, 'error', "事務システムAPI");
      return null;
    }
    //TODO 弓削さんの場合＋1000円 期間講習の対応なので関係ない？
    if($teacher_id == 1){
      $res["data"]['lesson_fee'] = intval($res["data"]['lesson_fee'])+1000;
    }

    return $controller->api_response(200, "", "" , $res["data"]);
  }
  public function get_lesson_fee(){
    if($this->setting->is_teaching()!=true){
      return null;
    }
    $user = $this->user->details();
    if($user->role!='student'){
      return null;
    }
    $lesson = $this->setting->lesson(true);
    $course = $this->setting->course(true);
    $grade = $user->tag_value('grade');
    $course_minutes = $this->setting->course_minutes(true);
    //体験の場合、まだis_enable=trueの状況で、feeを確定することになる
    $settings = $user->get_calendar_settings(["search_status"=>["new", "confirm", "fix"]]);
    $lesson_week_count = 0;
    foreach($settings as $setting){
      if($lesson==$setting->lesson(true) &&
        $course == $setting->course(true)){
          if($lesson_week_count < 3) $lesson_week_count++;
      }
    }
    $t = $this->setting->user->details('teachers');
    $res = UserCalendarMemberSetting::get_api_lesson_fee($lesson, $course, $course_minutes, $lesson_week_count, $grade, $user->is_juken(), $t->id);

    return $res;
  }
  public function set_api_lesson_fee($lesson_fee=null){
    if($this->setting->is_teaching()!=true){
      return null;
    }
    $user = $this->user->details();
    if($user->role!='student'){
      return null;
    }
    $lesson = $this->setting->lesson(true);
    $course = $this->setting->course(true);
    $grade = $user->tag_value('grade');
    $course_minutes = $this->setting->course_minutes(true);
    $jukensei_flag = 0;
    if($user->is_juken()==true){
      $jukensei_flag = 1;
    }
    //体験の場合、まだis_enable=trueの状況で、feeを確定することになる
    $settings = $user->get_calendar_settings(["search_status"=>["new", "confirm", "fix"]]);
    $lesson_week_count = 0;
    foreach($settings as $setting){
      if($lesson==$setting->lesson(true) &&
        $course == $setting->course(true)){
          if($lesson_week_count < 3) $lesson_week_count++;
      }
    }

    $setting_details = $this->setting->details(1);
    $teacher = $this->setting->user->details('teachers');

    if($lesson_fee==null){
      $res = $this->get_lesson_fee();
      if($res==null){
        return $this->error_response("get_api_lesson_fee error");
      }
      $lesson_fee = $res['data']['lesson_fee'];
    }
    $subject = '';
    if($this->setting->get_tag_value('lesson')==2 && $this->setting->has_tag('english_talk_lesson', 'chinese')==true){
      $subject= $this->setting->get_tag_value('subject');
    }
    elseif($this->setting->get_tag_value('lesson')==4){
      $subject= $this->setting->get_tag_value('kids_lesson');
    }
    $tuition = $this->get_tuition();
    if($tuition == null){
      Tuition::add([
        'student_id' => $user->id,
        'teacher_id' => $teacher->id,
        'tuition' => $lesson_fee,
        'title' => $setting_details['title'],
        'remark' => '',
        "lesson" => $lesson,
        "course_type" => $course,
        "course_minutes" => $course_minutes,
        "grade" => $grade,
        "lesson_week_count" => $lesson_week_count,
        "subject" => $subject,
        "create_user_id" => $this->setting->user_id,
        "start_date" => '9999-12-31',
        "end_date" => '9999-12-31',
      ]);
    }
    else {
      $tuition->update([
        'title' => $setting_details['title'],
        'tuition' => $lesson_fee,
      ]);
    }
    return $this->api_response(200, '', '');
  }
  //TODO : status_updateをcalendar_setting向けに最適帰化
  public function status_update($status, $remark, $login_user_id, $is_send_mail=true, $is_send_teacher_mail=true){
    $is_update = false;
    $login_user = User::where('id', $login_user_id)->first();
    $update_form = ['status' => $status, 'remark' => $remark, 'access_key' => $this->create_token(1728000)];
    $param = [];

    if(!isset($login_user)){
      return false;
    }
    switch($status){
      case "remind":
        $is_send_teacher_mail = false;
        $is_send_mail = true;
        //リマインド操作＝事務 or 講師
        if($this->setting->status!='confirm'){
          $is_send_mail = false;
        }
        $status = $this->setting->status;
        $param['token'] = $this->access_key;
        if(empty($param['token'])){
          $this->update(['access_key' => $update_form['access_key']]);
          $param['token'] = $update_form['access_key'];
        }
        break;
      case "confirm":
        if($this->status=='new' || $this->status=='confirm'){
          $is_update = true;
          $is_send_teacher_mail = false;
        }
        break;
      case "fix":
        if($this->setting->status=='confirm' || $this->setting->status=='new'){
          $is_update = true;
        }
        break;
      case "cancel":
        if($this->status=='confirm' || $this->status=='fix'){
          $is_update = true;
        }
        break;
    }
    if($is_update){
      $this->update($update_form);
      $param['token'] = $update_form['access_key'];
      $res = $this->_office_system_api('PUT');
      switch($status){
        case "confirm":
          $this->setting->status_to_confirm($remark, $this->user_id);
          break;
        case "fix":
          $this->setting->status_to_fix($remark, $this->user_id);
          break;
        case "cancel":
          $this->setting->status_to_cancel($remark, $this->user_id);
          break;
      }
    }

    \Log::warning("UserCalendarMemberSetting::status_update(".$status."):".$this->id."/user_id=".$this->user_id);
    //ステータス別のメッセージ文言取得
    $title = __('messages.mail_title_calendar_setting_'.$status);
    $type = 'text';
    $template = 'calendar_setting_'.$status;

    $u = $this->user->details();
    $param['login_user'] = $login_user->details();
    $param['user'] = $u;
    $param['user_name'] = $u->name();
    $param['item'] = $this->setting->details($this->user_id);
    $param['send_to'] = $u->role;

    if($is_send_mail){
      //このユーザーにメール送信
      $this->user->send_mail($title, $param, $type, $template);
    }
    if($is_send_teacher_mail==true && $u->role!="teacher"){
      //※このmemberが講師の場合はすでに送信しているため、送らない
      if(!($is_send_mail && $this->setting->user_id == $this->user_id)){
        $this->setting->teacher_mail($title, $param, $type, $template);
      }
    }
  }
}
