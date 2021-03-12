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
use App\Models\TuitionMaster;
use App\Models\Agreement;
use App\Models\AgreementStatement;

/**
 * App\Models\UserCalendarMemberSetting
 *
 * @property int $id
 * @property int $user_calendar_setting_id カレンダー設定ID
 * @property int $user_id 参加者設定
 * @property string $status ステータス/ new=新規登録 fix=有効 cancel=無効
 * @property string|null $remark 備考
 * @property string $access_key アクセスキー
 * @property int $create_user_id 作成ユーザーID
 * @property int $setting_id_org 事務システム側のID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserCalendar $calendar
 * @property-read User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserCalendar[] $exchanged_calendars
 * @property-read mixed $created_date
 * @property-read mixed $status_name
 * @property-read mixed $updated_date
 * @property-read \App\Models\PlaceFloorSheat $place_floor_sheat
 * @property-read UserCalendarSetting $setting
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMemberSetting fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMember findRestStatuses($is_not)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMember findRestType($is_not)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMemberSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMemberSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMemberSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMember searchExchangeLimitDate($from_date, $to_date)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMemberSetting searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarMember searchWord($word)
 * @mixin \Eloquent
 */
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
  public function agreement_statements(){
    return $this->belongsToMany('App\Models\AgreementStatement','common.user_calendar_member_setting_agreement_statement','user_calendar_member_setting_id','agreement_statement_id')->withTimestamps();
  }

  public function enable_agreement_statements(){
    return $this->agreement_statements()->enable();
  }
  public function dispose($login_user_id){
    $login_user = User::where('id', $login_user_id)->first();
    if(!isset($login_user)) return false;
    $user_id = $this->user_id;
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
    //契約処理
    $this->agreement_update($this->user_id);
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
        $student_no = $user->get_tag_value('student_no');
      }
      $user = $_member->user->details('teachers');
      if($user->role==="teacher"){
        $teacher_no = $user->get_tag_value('teacher_no');
      }
      $user = $_member->user->details('managers');
      if($user->role==="manager" || $user->role==="staff"){
        $manager_no = $user->get_tag_value('manager_no');
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
  public function is_recess_or_unsubscribe(){
    $u = $this->user->details();
    if($u->role=='student'){
      if(!empty($u->recess_start_date) && !empty($u->recess_end_date)){
        if(strtotime($this->setting->enable_start_time) > strtotime($u->recess_start_date) &&
          strtotime($this->setting->enable_start_time) < strtotime($u->recess_end_date)){
            return true;
        }
      }
      if(!empty($u->unsubscribe_date)){
        if(strtotime($this->setting->enable_start_time) > strtotime($u->unsubscribe_date)){
            return true;
        }
      }
    }
    return false;
  }

  public function get_tuition(){
    if($this->setting->is_teaching()!=true){
      return null;
    }
    $user = $this->user->details();
    if($user->role!='student'){
      return null;
    }
    if($this->agreement_statements->count() > 0){
      return $this->agreement_statements->first()->tuition;
    }
    $lesson = $this->setting->lesson(true);
    $course = $this->setting->course(true);
    $grade = $user->get_tag_value('grade');
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
    return $tuition->tuition;
  }
  static public function get_api_lesson_fee($lesson, $course, $course_minutes, $lesson_week_count, $grade, $is_juken=false, $teacher_id, $subject){
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
    if($subject!='dance'){
      $_url .='&lesson_week_count='.$lesson_week_count;
    }
    switch($subject){
      case "abacus":
      case "dance":
      case "chinese":
        $replace_subject = [
          "abacus" => 5,
          "dance" => 49,
          "chinese" => 43,
        ];
        $_url .='&subject='.$replace_subject[$subject];
        break;
    }
    \Log::warning("get_api_lesson_fee(".$_url.")");

    $controller = new Controller;
    $request = new Request;
    $res = $controller->call_api($request, $_url, 'GET', null);
    $message = "";
    $message = "事務システムAPI \nRequest:".$_url."\n".$message;
    if(empty($res)){
      $message .= "\n事務システムAPIエラー:".$_url."\nresponseなし";
      \Log::Error($message);
      @$controller->send_slack($message, 'error', "事務システムAPI");
      return $controller->error_response();
    }
    if(!isset($res["status"]) || $res["status"] != "success"){
      $message .= "\n事務システムAPIエラー:".$_url."\nstatus=".$res["status"];
      \Log::Error($message);
      @$controller->send_slack($message, 'error', "事務システムAPI");
      return $controller->error_response($res["message"], $res["description"] , $res["data"]);
    }
    if(!isset($res["data"]["lesson_fee"])){
      $message .= "\n事務システムAPIエラー:".$_url."\nstatus=".$res["status"];
      @$controller->send_slack($message, 'error', "事務システムAPI");
      return $controller->error_response($res["message"], $res["description"] , $res["data"]);
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
    $grade = $user->get_tag_value('grade');
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
    $subject = "";
    if($this->setting->has_tag('lesson',2)==true && $this->setting->has_tag('english_talk_lesson','chinese')==true){
      $subject = "chinese";
    }
    else if($this->setting->has_tag('lesson',4)==true && $this->setting->has_tag('kids_lesson','abacus')==true){
      $subject = "abacus";
    }
    else if($this->setting->has_tag('lesson',4)==true && $this->setting->has_tag('kids_lesson','dance')==true){
      $subject = "dance";
    }

    $res = UserCalendarMemberSetting::get_api_lesson_fee($lesson, $course, $course_minutes, $lesson_week_count, $grade, $user->is_juken(), $t->id, $subject);

    return $res;
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
        unset($update_form['status']);
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
          $is_send_teacher_mail = false;
        break;
      case "fix":
        if($this->user->details()->role == 'student'){
          $agreement = Agreement::add_from_member_setting($this->id);
        }
        break;
      }
    $this->update($update_form);
    $param['token'] = $update_form['access_key'];
    $res = $this->_office_system_api('PUT');
    $this->setting->set_status();

    \Log::warning("UserCalendarMemberSetting::status_update(".$status."):".$this->id."/user_id=".$this->user_id);
    //ステータス別のメッセージ文言取得
    $title = __('messages.mail_title_calendar_setting_'.$status);
    $type = 'text';
    $template = 'calendar_setting_'.$status;

    $u = $this->user->details();
    $param['login_user'] = $login_user->details();
    $param['user'] = $u;
    $param['user_name'] = $u->name();
    $param['notice'] = $remark;
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
  public function get_rest_result(){
    return "";
  }

  public function get_tuition_master(){
    $setting = $this->setting->details();
    //2020年4月1日以前のユーザーは0円で返す
    if( strtotime($this->user->created_at) > strtotime("2020/04/01") ){
      $user_created_date = date('Y/m/d',strtotime($this->user->created_at));
      $tuition_master = TuitionMaster::where('lesson',$setting->lesson(true))
                              ->where('grade',$this->user->details()->get_tag_value('grade'))
                              ->where('course_type',$setting->get_tag_value('course_type'))
                              ->where('course_minutes',$setting['course_minutes'])
                              ->where('lesson_week_count',$this->user->get_enable_calendar_setting_count($setting->lesson(true)))
                              ->where('is_exam',$this->user->details()->is_juken())
                              ->whereDate('start_date','<',$user_created_date)
                              ->whereDate('end_date','>',$user_created_date)->get();
      if($tuition_master->count() > 0){
        $tuition = $tuition_master->first()->tuition;
      }else{
        //なかったら0円
        $tuition = 0;
      }
      return $tuition;
    }else{
      //昔の人は0円
      return 0;
    }
  }
}
