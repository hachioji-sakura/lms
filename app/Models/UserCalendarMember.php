<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Models\GeneralAttribute;
use App\Models\Lecture;
use App\Models\Trial;

class UserCalendarMember extends Model
{
  protected $table = 'user_calendar_members';
  protected $guarded = array('id');
  protected $api_hosturl = "https://hachiojisakura.com/sakura-api";
  public $api_endpoint = [
    "GET" =>  "api_get_onetime_schedule.php",
    "PUT" =>  "api_update_onetime_schedule.php",
    "POST" =>  "api_insert_onetime_schedule.php",
    "DELETE" =>  "api_delete_onetime_schedule.php",
  ];
  public static $rules = array(
      'user_id' => 'required',
  );
  public function calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'calendar_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function add($form){
  }
  public function status_name(){
    $status_name = "";
    switch($this->status){
      case "new":
        return "予定(下書き)";
      case "confirm":
        return "予定確認中";
      case "fix":
        return "授業予定";
      case "cancel":
        return "キャンセル";
      case "rest":
        return "休み";
      case "absence":
        return "欠席";
      case "presence":
        return "出席済み";
    }
    return "";
  }
  public function office_system_api($method){
    if($this->schedule_id == 0 && $method=="PUT") return;
    if($this->schedule_id == 0 && $method=="DELETE") return;
    if($this->schedule_id > 0 && $method=="POST") return;

    $_url = $this->api_hosturl.'/'.$this->api_endpoint[$method];

    //事務システムのAPIは、GET or POSTなので、urlとともに、methodを合わせる
    $_method = "GET";
    if($method!=="GET") $_method = "POST";
    $student_no = "";
    $teacher_no = "";
    $manager_no = "";
    foreach($this->calendar->members as $_member){
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
    \Log::warning("事務システムAPI student_no=:".$student_no."\nteacher_no=".$teacher_no);

    $user = $this->user->details('teachers');
    if(($this->calendar->work==6 || $this->calendar->work==7 || $this->calendar->work==8)
        && $user->role==="teacher" && !empty($student_no)){
      //講師がメンバーかつ、生徒が取得可能な場合　＝　授業予定のカレンダー
      //生徒がメンバーかつ、講師が取得可能時に処理を行うので、APIは無視
      \Log::warning("授業予定の場合、参加者が講師だけではAPIを実行できない");
      return null;
    }

    $lesson = 0;
    $tags =  $this->calendar->tags->where('tag_key', 'lesson')->first();
    if(isset($tags)) $lesson = $tags->tag_value;

    $course = 0;
    $tags =  $this->calendar->tags->where('tag_key', 'course_type')->first();
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

    $place = GeneralAttribute::place($this->calendar->place)->first();
    $place_text = "";
    if(isset($place)){
      //場所の指定はidではなくあえてテキストを渡してみる
      $place_text = $place->attribute_name;
    }

    $replace_place = config('replace.place');
    if(isset($replace_place[$this->calendar->place])){
      $place_text = $replace_place[$this->calendar->place];
    }

    $__user_id = $student_no;
    switch($this->calendar->work){
      case 9:
        //事務のスケジュール
        $__user_id = $manager_no;
        $teacher_no = "";
        $student_no = "";
        break;
    }
    //振替のカレンダーID・事務システムのスケジュールIDに置き換え
    $altsched_id = 0;
    if($this->calendar->exchanged_calendar_id > 0){
      //振替元のメンバーを取得
      $exchanged_calendar_member = UserCalendarMember::where('calendar_id', $this->calendar->exchanged_calendar_id)
        ->where('user_id', $this->user_id)->get();
      $altsched_id = $exchanged_calendar_member->schedule_id;
    }


    $postdata =[];
    switch($method){
      case "PUT":
      case "POST":
        $postdata = [
          "user_id" => $__user_id,
          "student_no" => $student_no,
          "teacher_id" => $teacher_no,
          "trial_id" => $this->calendar->trial_id,
          "ymd" => date('Y-m-d', strtotime($this->calendar->start_time)),
          "starttime" => date('H:i:s', strtotime($this->calendar->start_time)),
          "endtime" => date('H:i:s', strtotime($this->calendar->end_time)),
          "lecture_id" => $lecture_id_org,
          "subject_expr" => implode (',', $this->calendar->subject()),
          "work_id" => $this->calendar->work,
          "place_id" => $place_text,
          "altsched_id" => $altsched_id,
          "cancel" => "",
          "confirm" => "",
          "temporary" => "111",
        ];
        break;
    }
    if($method==="PUT" || $method==="DELETE"){
      $postdata['id'] = $this->schedule_id;
    }

    switch($this->calendar->status){
      case "new":
        //生徒確定ではないので、空にする
        $postdata['updateuser'] = $teacher_no;
        $postdata['temporary'] = '1';
        break;
      case "confirm":
        $postdata['updateuser'] = $teacher_no;
        $postdata['temporary'] = '11';
        break;
      case "fix":
        //生徒確定
        $postdata['updateuser'] = $student_no;
        break;
    }
    switch($this->status){
      case "cancel":
        //3.12確認：キャンセル：cにする（論理削除にすると表示できなくなるため）
        $postdata['cancel'] = 'c';
        $postdata['updateuser'] = $student_no;
        break;
      case "rest":
        //3.12確認：事前連絡あり休み＝aにする、よしなに休み判定をするとのこと
        $postdata['cancel'] = 'a';
        $postdata['updateuser'] = $student_no;
        break;
      case "absence":
        //3.12確認：欠席＝a2にする
        $postdata['cancel'] = 'a2';
        $postdata['updateuser'] = $teacher_no;
        break;
      case "presence":
        $postdata['confirm'] = 'f';
        $postdata['updateuser'] = $teacher_no;
        break;
    }
    $message = "";
    foreach($postdata as $key => $val){
      $message .= '['.$key.':'.$val.']';
    }
    $res = $this->call_api($_url, $_method, $postdata);
    $str_res = json_encode($res);
    \Log::info("事務システムAPI Request:".$_url."\n".$message);
    \Log::info("事務システムAPI Response:".$_url."\n".$str_res);
    if(empty($res)){
      @$this->send_slack("事務システムAPIエラー:".$_url."\nresponseなし", 'error', "事務システムAPIエラー");
      return null;
    }
    if($res["status"] != 0){
      @$this->send_slack("事務システムAPIエラー:".$_url."\nstatus=".$res["status"], 'error', "事務システムAPIエラー");
      return null;
    }
    if($method==="POST" && $this->schedule_id==0){
      //事務システム側のIDを更新
      if(isset($res['id'])){
        $this->update(['schedule_id'=>$res["id"]]);
        \Log::info("事務システムAPI ID更新:".$res["id"]);
      }
      else{
        @$this->send_slack("事務システムAPIエラー:IDがとれない", 'warning', "事務システムAPIエラー");
      }
    }
    return $res;
  }
  protected function send_slack($message, $msg_type, $username=null, $channel=null) {
    $controller = new Controller;
    $res = $controller->send_slack($message, $msg_type, $username, $channel);
    return $res;
  }
  protected function call_api($url, $method, $data){
    $controller = new Controller;
    $req = new Request;
    $res = $controller->call_api($req, $url, $method, $data);
    return $res;
  }
}
