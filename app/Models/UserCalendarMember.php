<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Models\GeneralAttribute;
use App\Models\Lecture;
use App\Models\Trial;
use App\Models\Ask;

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
    'calendar_id' => 'required',
    'user_id' => 'required',
  );
  public function calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'calendar_id');
  }
  public function exchanged_calendar(){
    return $this->hasOne('App\Models\UserCalendar', 'exchanged_calendar_id', 'calendar_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function is_limit_over(){
    if($this->remark=='規定回数以上'){
      return true;
    }
    return false;
  }
  public function status_name(){
    $status_name = "";
    if(isset(config('attribute.calendar_status')[$this->status])){
      $status_name = config('attribute.calendar_status')[$this->status];
    }
    switch($this->status){
      case "fix":
        if($this->work==9) return "勤務予定";
      case "absence":
        if($this->work==9) return "欠勤";
      case "presence":
      if($this->work==9) return "出勤";
    }
    return $status_name;
  }
  public function update_rest_type($update_rest_type){
    $res = $this->_office_system_api('PUT', $update_rest_type);
    return $this;
  }
  public function remark(){
    $remark = "";
    if(!empty(trim($this->remark))) $remark = trim($this->remark);
    if($this->rest_type == 'a1'){
      $remark = '休み1:'.$remark;
    }
    if($this->rest_type == 'a2'){
      $remark = '休み2:'.$remark;
    }
    return $remark;
  }
  public function office_system_api($method){
    return $this->_office_system_api($method);
  }
  public function _office_system_api($method, $update_rest_type="", $is_rest_cancel=false){
    if($this->schedule_id == 0 && $method=="PUT") return null; ;
    if($this->schedule_id == 0 && $method=="DELETE") return null;
    if($this->schedule_id > 0 && $method=="POST") return null;
    //rest_cancelの場合、API実行不要
    if($this->status=='rest_cancel') return null;
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
    $user = null;
    if($this->calendar->work==9){
      $user = $this->user->details('managers');
    }
    else {
      $user = $this->user->details('teachers');
    }
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
    if($this->lecture_id > 0){
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
        ->where('user_id', $this->user_id)->first();
      if(isset($exchanged_calendar_member)){
        $altsched_id = $exchanged_calendar_member->schedule_id;
      }
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
          //"cancel" => "",
          //"confirm" => "",
          //"temporary" => "111",
        ];
        break;
    }
    if($method==="PUT" || $method==="DELETE"){
      $postdata['id'] = $this->schedule_id;
    }
    //TODO : temporary / cancel / confirm 等のフィールドは不要
    $type = $this->status;
    if($type != $this->status){
      if($type=='new') $type = $this->calendar->status;
    }
    $postdata['type'] = $type;

    if($is_rest_cancel==true){
      //休み取り消しをAPIで送信
      $postdata['type'] = "rest_cancel";
    }
    if($this->calendar->status==6 || $this->calendar->status==7 || $this->calendar->status==8){
      $postdata['updateuser'] = $teacher_no;
      switch($this->calendar->status){
        case "new":
          //生徒確定ではないので、空にする
          //$postdata['temporary'] = '1';
          break;
        case "confirm":
          //$postdata['temporary'] = '11';
          break;
        case "fix":
          //生徒確定(休み取り消し時考慮）
          //$postdata['cancel'] = '';
          //$postdata['confirm'] = '';
          $postdata['updateuser'] = $student_no;
          break;
        case "rest_cancel":
          $postdata['updateuser'] = $student_no;
          break;
        case "cancel":
          //3.12確認：キャンセル：cにする（論理削除にすると表示できなくなるため）
          //$postdata['cancel'] = 'c';
          $postdata['updateuser'] = $student_no;
          break;
        case "rest":
          //3.12確認：事前連絡あり休み＝aにする、よしなに休み判定をするとのこと
          //$postdata['cancel'] = 'a';
          $postdata['updateuser'] = $student_no;
          break;
        case "absence":
          //3.12確認：欠席＝a2にする
          //$postdata['confirm'] = 'a2';
          break;
        case "presence":
          //$postdata['confirm'] = 'f';
          break;
        default:
          $postdata['updateuser'] = $student_no;
          break;
      }
    }
    else {
      $postdata['updateuser'] = $__user_id;
    }

    //休み１⇔休み２の変更のための対応
    if(!empty($update_rest_type)) {
      //$postdata['cancel'] = $update_rest_type;
    }
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
    if(empty($res)){
      @$this->send_slack("事務システムAPIエラー:".$_url."\nresponseなし", 'error', "事務システムAPIエラー");
      return null;
    }
    if($res["status"] != 0){
      @$this->send_slack("事務システムAPIエラー:".$_url."\nstatus=".$res["status"], 'error', "事務システムAPIエラー");
      return null;
    }
    $schedule_id = $this->schedule_id;
    if($method==="POST" && $schedule_id==0){
      //事務システム側のIDを更新
      if(isset($res['id'])){
        $this->update(['schedule_id'=>$res["id"]]);
        \Log::info("事務システムAPI ID更新:".$res["id"]);
        $schedule_id = $res["id"];
      }
      else{
        @$this->send_slack("事務システムAPIエラー:IDがとれない", 'warning', "事務システムAPIエラー");
      }
    }
    //cancel_reasonの取得
    $_url = $this->api_hosturl.'/'.$this->api_endpoint["GET"].'?id='.$schedule_id;
    $res = $this->call_api($_url, $_method, $postdata);
    if(isset($res) && isset($res["data"]) && count($res["data"])==1){
      $message = "";
      foreach($res["data"][0] as $key => $val){
        $message .= '['.$key.':'.$val.']';
      }
      $remark = trim($res["data"][0]["cancel_reason"]);
      $cancel = "";
      if(isset($res["data"][0]["cancel"])) $cancel = trim($res["data"][0]["cancel"]);
      $update = [];
      $is_update = false;
      if(!empty($remark)  && $this->remark != $remark){
        $update['remark'] = $remark;
        $is_update = true;
        $this->update(['remark' => $remark]);
      }
      if(!empty($cancel)  && $this->rest_type != $cancel){
        $update['rest_type'] = $cancel;
        $is_update = true;
      }
      if($is_update==true){
        $this->update($update);
        @$this->send_slack("休み判別結果：".$cancel.':'.$remark."\ndata:\n".$message, 'warning', "事務システムAPI");
      }
    }
    return $res;
  }
  public function send_mail($title, $param, $type, $template){
    $param['user'] = $this->user->details();
    $controller = new Controller;
    $res = $controller->send_mail($this->get_mail_address(), $title, $param, $type, $template);
    return $res;
  }
  private function get_mail_address(){
    \Log::info("-----------------get_mail_address------------------");
    $u = $this->user->details();
    $email = '';
    \Log::info($u->role);
    if($u->role==='student'){
      $student_id = $this->user->student->id;
      $relations = StudentRelation::where('student_id', $student_id)->get();
      foreach($relations as $relation){
        //TODO 先にとれたユーザーを操作する親にする（修正したい）
        $user_id = $relation->parent->user->id;
        $email = $relation->parent->user->email;
        \Log::info("relation=".$user_id.":".$email);
        //TODO 安全策をとるテスト用メールにする
        //$email = 'yasui.hideo+u'.$user_id.'@gmail.com';
        break;
      }
    }
    else {
      $email = $u->email;
    }
    \Log::info("-----------------get_mail_address[$email]------------------");
    return $email;
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
  public function rest_cancel_ask($create_user_id){
    //期限＝予定前日まで
    $ask = Ask::add("rest_cancel", [
      "end_date" => date("Y-m-d", strtotime("-1 day ".$this->calendar->start_time)),
      "body" => "",
      "target_model" => "user_calendar_members",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $this->user_id,
      "charge_user_id" => $this->calendar->user_id,
    ]);
    return $ask;
  }
  public function rest_cancel($is_exec=true){
    if($is_exec==true){
      //授業予定に戻す
      $this->update(['status' => 'fix']);
      $this->_office_system_api('PUT', '', true);
    }
    else {
      //休みに戻す
      $this->update(['status' => 'rest']);
    }
  }
  public function lecture_cancel_ask($create_user_id){
    //期限＝予定前日まで
    $ask = Ask::add("lecture_cancel", [
      "end_date" => date("Y-m-d", strtotime("-1 day ".$this->calendar->start_time)),
      "body" => "",
      "target_model" => "user_calendar_members",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $this->user_id,
      "charge_user_id" => 1,
    ]);
    return $ask;
  }
  public function lecture_cancel($is_exec=true, $login_user_id){
    if($is_exec==true){
      //休講に更新
      $this->calendar->update(['status' => 'lecture_cancel']);
      foreach($this->calendar->members as $member){
        $member->update(['status' => 'lecture_cancel']);
      }
      $this->calendar->office_system_api('PUT');
    }
    else {
      //授業予定に戻す
      $this->update(['status' => 'fix']);
    }
  }

}
