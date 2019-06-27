<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarMemberSetting extends UserCalendarMember
{
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

  public function office_system_api($method){
    if($this->setting_id_org == 0 && $method=="PUT") return null;;
    if($this->setting_id_org == 0 && $method=="DELETE") return null;;
    if($this->setting_id_org > 0 && $method=="POST") return null;;

    $_url = $this->api_hosturl.'/'.$this->api_endpoint[$method];

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
    //TODO : 6/22 : 事務システム側の場所最適化すべき
    //Googleカレンダー名：フロアに変換する（フロアIDが取れる想定）
    $place_text = $this->setting->place_floor->_convert_offie_system_place();

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
          "startdate" => $this->setting->enable_start_date,
          "enddate" => $this->setting->enable_end_date,
          "starttime" => $this->setting->from_time_slot,
          "endtime" => $this->setting->to_time_slot,
          "lecture_id" => $lecture_id_org,
          "subject_expr" => implode (',', $this->setting->subject()),
          "work_id" => $this->setting->work,
          "place_id" => $place_text,
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
}
