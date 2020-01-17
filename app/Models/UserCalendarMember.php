<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Models\GeneralAttribute;
use App\Models\Lecture;
use App\Models\Trial;
use App\Models\Ask;
use App\Models\PlaceFloor;
use App\Models\UserCalendar;
use App\Models\UserCalendarTag;
use App\User;
use App\Models\Traits\Common;
use View;
class UserCalendarMember extends Model
{
  use Common;
  protected $table = 'lms.user_calendar_members';
  protected $guarded = array('id');
  public $api_domain = '/sakura-api';
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
  public function place_floor_sheat(){
    return $this->belongsTo('App\Models\PlaceFloorSheat', 'place_floor_sheat_id');
  }
  public function exchanged_calendars(){
    //振替先のカレンダー
    return $this->hasMany('App\Models\UserCalendar', 'exchanged_calendar_id', 'calendar_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  //振替対象の場合true
  public function is_exchange_target(){
    if(empty($this->exchange_limit_date)) return false;
    $diff = strtotime($this->exchange_limit_date) - strtotime(date('Y-m-d'));
    if($diff >= 0) {
      //期限内
      return true;
    }
    return false;
  }
  //振替可能残り時間
  public function get_exchange_remaining_time(){
    $exchanged_minutes = 0;
    foreach($this->exchanged_calendars as $exchanged_calendar){
      if($exchanged_calendar->status=='cancel') continue;
      //if($exchanged_calendar->status=='new') continue;
      $exchanged_minutes += intval($exchanged_calendar->course_minutes);
    }
    return $this->calendar->course_minutes - $exchanged_minutes;
  }
  //休み取り消し可能な場合=true
  public function is_rest_cancel_enable(){
    \Log::warning("-----is_rest_cancel_enable-----");
    if($this->status!='rest') return false;
    if($this->is_exchange_target()==true){
      $is_find = false;
      foreach($this->exchanged_calendars as $exchanged_calendar){
        if($exchanged_calendar->status=='cancel') continue;
        $is_find = true;
        break;
      }
      //振替が登録されている場合は、休み取り消しはできない
      if($is_find==true) return false;
    }
    return true;
  }
  public function remind($login_user_id){
    \Log::warning("member.remind");
    $this->status_update("remind", "", $login_user_id);
  }
  public function status_update($status, $remark, $login_user_id, $is_send_mail=true, $is_send_teacher_mail=true){
    $is_update = false;
    $login_user = User::where('id', $login_user_id)->first();
    $update_form = ['status' => $status, 'remark' => $remark, 'access_key' => $this->create_token(1728000)];
    if(!isset($login_user)){
      return false;
    }
    switch($status){
      case "remind":
        $is_send_teacher_mail = false;
        $is_send_mail = true;
        //リマインド操作＝事務 or 講師
        if($this->calendar->status!='confirm'){
          $is_send_mail = false;
        }
        $status = $this->calendar->status;
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
        if($this->calendar->status=='confirm'){
          $is_update = true;
        }
        break;
      case "cancel":
        if($this->status=='confirm' || $this->status=='fix'){
          $is_update = true;
        }
        break;
      case "rest":
        if($this->status=='fix'){
          $is_update = true;
        }
        break;
      case "presence":
        $is_send_mail = false;
        $is_send_teacher_mail = false;
        if($this->status=='fix' || $this->status=='absence'){
          $is_update = true;
        }
        break;
      case "absence":
        $is_send_mail = false;
        $is_send_teacher_mail = false;
        if($this->status=='fix' || $this->status=='presence'){
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
          $this->calendar->status_to_confirm($remark, $this->user_id);
          break;
        case "fix":
          $this->calendar->status_to_fix($remark, $this->user_id);
          break;
        case "cancel":
          $this->calendar->status_to_cancel($remark, $this->user_id);
          break;
        case "rest":
          $this->calendar->status_to_rest($remark, $this->user_id);
          break;
        case "absence":
          $this->calendar->status_to_absence($remark, $this->user_id);
          break;
        case "presence":
          $this->calendar->status_to_presence($remark, $this->user_id);
          break;
      }
    }

    \Log::warning("UserCalendarMember::status_update(".$status."):".$this->id."/user_id=".$this->user_id);
    //ステータス別のメッセージ文言取得
    $title = __('messages.mail_title_calendar_'.$status);
    $type = 'text';
    $template = 'calendar_'.$status;

    $u = $this->user->details();
    $param['login_user'] = $login_user->details();
    $param['user'] = $u;
    $param['user_name'] = $u->name();
    $param['item'] = $this->calendar->details($this->user_id);
    $param['send_to'] = $u->role;
    $param['is_proxy'] = false;
    if(($param['login_user']->role=='teacher' || $param['login_user']->role=='manager') && $u->role == 'student'){
      //代理の場合
      $param['is_proxy'] = true;
    }

    if($is_send_mail){
      //このユーザーにメール送信
      $this->user->send_mail($title, $param, $type, $template);
    }
    if($is_send_teacher_mail==true && $u->role!="teacher"){
      //※このmemberが講師の場合はすでに送信しているため、送らない
      if(!($is_send_mail && $this->calendar->user_id == $this->user_id)){
        $this->calendar->teacher_mail($title, $param, $type, $template);
      }
    }
  }
  public function is_recess_or_unsubscribe(){
    $u = $this->user->details();
    if($u->role=='student'){
      if(!empty($u->recess_start_date) && !empty($u->recess_end_date)){
        if(strtotime($this->calendar->start_time) > strtotime($u->recess_start_date) &&
          strtotime($this->calendar->start_time) < strtotime($u->recess_end_date)){
            return true;
        }
      }
      if(!empty($u->unsubscribe_date)){
        if(strtotime($this->calendar->start_time) > strtotime($u->unsubscribe_date)){
            return true;
        }
      }
    }
    return false;
  }
  public function status_name(){
    $status = $this->status;
    if($this->is_recess_or_unsubscribe()==true){
      //生徒が退会日以降、休会日範囲の場合、cancel表記
      $status = 'cancel';
    }
    if(app()->getLocale()=='en') return $status;
    $status_name = "";
    if(isset(config('attribute.calendar_status')[$status])){
      $status_name = config('attribute.calendar_status')[$status];
    }
    switch($status){
      case "fix":
        if($this->work==9) return "勤務予定";
      case "absence":
        if($this->work==9) return "欠勤";
      case "presence":
      if($this->work==9) return "出勤";
    }
    return $status_name;
  }
  public function is_active(){
    if($this->status=='cancel') return false;
    if($this->status=='rest') return false;
    if($this->status=='lecture_cancel') return false;
    return true;
  }
  public function update_rest_type($update_rest_type, $update_rest_result){
    $this->update([
      'status' => 'rest',
      'rest_type' => $update_rest_type,
      'rest_result' => $update_rest_result,
    ]);
    $res = $this->_office_system_api('PUT', $update_rest_type, $update_rest_result);

    if($this->calendar->status=='absence'){
      //欠席→休みに変更する場合
      $is_absence = false;
      foreach($this->calendar->get_students() as $member){
        if($member->status == 'absence'){
          $is_absence = true;
          break;
        }
      }
      if($is_absence==false){
        //生徒の欠席ステータスがない場合、休みステータスに変更
        $this->calendar->update(['status' => 'rest']);
      }
    }

    return $this;
  }
  public function rest_result(){
    $rest_result = "";
    if(!empty(trim($this->rest_result))) $rest_result = trim($this->rest_result);
    if($this->rest_type == 'a1'){
      $rest_result = '休み1:'.$rest_result;
    }
    if($this->rest_type == 'a2'){
      $rest_result = '休み2:'.$rest_result;
    }

    return $rest_result;
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    $c = 0;
    foreach($this->calendar->get_students() as $member){
      if($member->id == $this->id) continue;
      $c++;
    }
    $this->office_system_api("DELETE");
    if($c===0){
      //最後に削除した生徒の場合、カレンダーも削除
      UserCalendar::where('id', $this->calendar_id)->delete();
      UserCalendarTag::where('calendar_id', $this->calendar_id)->delete();
      UserCalendarMember::where('calendar_id', $this->calendar_id)->delete();
    }
    else {
      $this->delete();
    }
    $this->user->send_mail($title, $param, $type, $template);

  }

  public function office_system_api($method){
    return $this->_office_system_api($method);
  }
  public function _office_system_api($method, $update_rest_type="", $update_rest_result='', $is_rest_cancel=false){
    if($this->schedule_id == 0 && $method=="PUT") return null; ;
    if($this->schedule_id == 0 && $method=="DELETE") return null;
    if($this->schedule_id > 0 && $method=="POST") return null;
    //rest_cancelの場合、API実行不要
    if($this->status=='rest_cancel') return null;
    $_url = config('app.management_url').$this->api_domain.'/'.$this->api_endpoint[$method];

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
      if($user->role==="manager" || $user->role==="staff"){
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
    if($this->calendar->lecture_id > 0){
      $lecture = Lecture::where('id', $this->calendar->lecture_id)->first();
      if(isset($lecture)){
        $lecture_id_org = $lecture->lecture_id_org;
      }
      else {
        //レクチャが取得できない=lesson ・ courseから取得
        $lecture = Lecture::where('lesson', $lesson)->where('course', $course)->first();
        if(isset($lecture)){
          $lecture_id_org = $lecture->lecture_id_org;
        }
      }
    }
    $__user_id = $student_no;
    if(empty($__user_id)) $__user_id = $teacher_no;
    if($this->calendar->work==9 && !empty($manager_no)){
      //事務のスケジュール
      $__user_id = $manager_no;
      $teacher_no = "";
      $student_no = "";
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
    $repetition_id = 0;
    if($this->calendar->user_calendar_setting_id > 0){
      //通常授業設定由来の場合
      foreach($this->calendar->setting->members as $_m){
        if($_m->user_id == $this->user_id){
          $repetition_id = $_m->setting_id_org;
        }
      }
    }

    $postdata = [
      "trial_id" => $this->calendar->trial_id,
      "ymd" => date('Y-m-d', strtotime($this->calendar->start_time)),
      "starttime" => date('H:i:s', strtotime($this->calendar->start_time)),
      "endtime" => date('H:i:s', strtotime($this->calendar->end_time)),
      "lecture_id" => $lecture_id_org,
      "subject_expr" => implode (',', $this->calendar->subject()),
      "work_id" => $this->calendar->work,
      "place_id" => $this->calendar->place_floor_id,
      "altsched_id" => $altsched_id,
      //"cancel" => "",
      //"confirm" => "",
      //"temporary" => "111",
    ];
    switch($method){
      case "POST":
        $postdata["user_id"] = $__user_id;
        $postdata["student_no"] = $student_no;
        $postdata["teacher_id"] = $teacher_no;
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


    //休み１⇔休み２の変更のための対応
    if(!empty($update_rest_type)) {
      \Log::info("事務システムAPI 休み種別強制変更:".$update_rest_type.'('.$update_rest_result.')');
      @$this->send_slack("事務システムAPI 休み種別強制変更:".$update_rest_type.'('.$update_rest_result.')', 'warning', "事務システムAPI");
      $postdata['cancel'] = $update_rest_type;
      $postdata['cancel_reason'] = $update_rest_result;
      //TODO type指定なしであれば、cancel + cancel_reasonの編集をAPI側で対応してくれる？
      //$postdata['type'] = 'special_cancel_reason';
    }
    else {
      //休み種別の変更ではない場合、タイプを指定する
      $postdata['type'] = $type;
      if($is_rest_cancel==true){
        //休み取り消しをAPIで送信
        $postdata['type'] = "rest_cancel";
      }
    }

    //TODO 更新者を取得しても、事務システム側のデータ単位が異なるので適切に設定することができない
    //このロジックはあまり意味がない
    $postdata['updateuser'] = $__user_id;
    \Log::warning("debug:".$__user_id);

    if($this->calendar->work==6 || $this->calendar->work==7 || $this->calendar->work==8){
      $postdata['updateuser'] = $teacher_no;
      switch($this->calendar->status){
        case "fix":
        case "rest_cancel":
        case "cancel":
        case "rest":
          $postdata['updateuser'] = $student_no;
          break;
      }
    }

    $message = "";
    foreach($postdata as $key => $val){
      $message .= '['.$key.':'.$val.']';
    }
    $res = $this->call_api($_url, $_method, $postdata);
    $message = "事務システムAPI \nRequest:".$_url."\n".$message;
    if(empty($res)){
      $message .= "\n事務システムAPIエラー:".$_url."\nresponseなし";
      \Log::Error($message);
      @$this->send_slack($message, 'error', "事務システムAPI");
      return null;
    }
    else{
      $str_res = json_encode($res);
      $message .= "\nResponse:"."\n".$str_res;
      @$this->send_slack($message, 'warning', "事務システムAPI");
      \Log::info($message);
    }

    if($res["status"] != 0){
      $message .= "\n事務システムAPIエラー:".$_url."\nstatus=".$res["status"];
      \Log::Error($message);
      @$this->send_slack($message, 'error', "事務システムAPI");
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
    else {
      if(isset($res) && isset($res["data"])){
        $message = "";
        foreach($res["data"] as $key => $val){
          $message .= '['.$key.':'.$val.']';
        }
        \Log::info("事務システムAPI 休み判別:".$_url."\n".$message);

        $cancel = "";
        $exchange_limit_date = "";
        $comment = "";
        $cancel_reason = "";
        if(isset($res["data"]["cancel"])) {
          $cancel = trim($res["data"]["cancel"]);
        }
        if(isset($res["data"]["altlimitdate"])){
          $exchange_limit_date = trim($res["data"]["altlimitdate"]);
        }
        if(isset($res["data"]["comment"])){
          $comment = trim($res["data"]["comment"]);
        }
        if(isset($res["data"]["cancel_reason"])){
          $cancel_reason = trim($res["data"]["cancel_reason"]);
        }

        $update = [];
        $is_update = false;
        if(!empty($comment)  && $this->remark != $comment){
          //comment -> remark
          $update['remark'] = $comment;
          $is_update = true;
        }
        if(!empty($cancel)  && $this->rest_type != $cancel){
          //cancel -> rest_type
          $update['rest_type'] = $cancel;
          $is_update = true;
        }
        //cancel_reasonは空になる可能性がある
        if($this->rest_result != $cancel_reason){
          //cancel_reason -> rest_result
          $update['rest_result'] = $cancel_reason;
          $is_update = true;
        }
        if(!empty($exchange_limit_date)  && $this->exchange_limit_date != $exchange_limit_date){
          $update['exchange_limit_date'] = $exchange_limit_date;
          $is_update = true;
        }

        if($is_update==true){
          $this->update($update);
          @$this->send_slack("休み判別結果：".$cancel.':'.$cancel_reason."\ndata:\n".$message, 'warning', "事務システムAPI");
        }
      }
    }
    return $res;
  }
  public function rest_cancel_ask($create_user_id){
    $user = User::where('id', $create_user_id)->first();
    $body = View::make('emails.forms.calendar')->with([
      'item'=>$this->calendar,
      'send_to' => 'teacher',
      'login_user' => $user->details(),
      ])->render();

    //期限＝予定前日まで
    $ask = Ask::add([
      "type" => "rest_cancel",
      "end_date" => date("Y-m-d", strtotime("-1 day ".$this->calendar->start_time)),
      "body" => $body,
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
      if($this->calendar->status=='rest'){
        $this->calendar->update(['status' =>'fix']);
      }
      $this->_office_system_api('PUT', '', '', true);
    }
    else {
      //休みに戻す
      $this->update(['status' => 'rest']);
    }
  }
  public function lecture_cancel_ask($create_user_id){
    $user = User::where('id', $create_user_id)->first();
    //期限＝予定前日まで
    $body = View::make('emails.forms.calendar')->with([
      'item'=>$this->calendar,
      'send_to' => 'manager',
      'login_user' => $user->details(),
      ])->render();

    $ask = Ask::add([
      "type" => "lecture_cancel",
      "end_date" => date("Y-m-d", strtotime("-1 day ".$this->calendar->start_time)),
      "body" => $body,
      "target_model" => "user_calendar_members",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $this->user_id,
      "charge_user_id" => 1,
    ]);
    return $ask;
  }
  public function lecture_cancel($is_exec=true){
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
  public function teacher_change_ask($create_user_id, $target_user_id){
    $user = User::where('id', $create_user_id)->first();
    //期限＝予定前日まで
    $body = View::make('emails.forms.calendar')->with([
      'item'=>$this->calendar,
      'send_to' => 'manager',
      'login_user' => $user->details(),
      ])->render();

    $ask = Ask::add([
      "type" => "lecture_cancel",
      "end_date" => date("Y-m-d", strtotime("-1 day ".$this->calendar->start_time)),
      "body" => $body,
      "target_model" => "user_calendar_members",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $target_user_id,
      "charge_user_id" => 1,
    ]);
    return $ask;
  }
  public function teacher_change($is_exec=true, $change_user_id){
    if($is_exec==true){
      //休講に更新
      $this->update(['user_id' => $change_user_id]);
      $this->calendar->update(['user_id' => $change_user_id]);
    }
  }
  public function already_ask_data($data){
    $form = [
      'type' => $data['type'],
      'status' => $data['status'],
      'target_model' => str_replace('lms.', '',$this->table),
      'target_model_id' => $this->id,
    ];
    if(isset($data['charge_user_id'])){
      $form['charge_user_id'] = $data['charge_user_id'];
    }
    else {
      $form['charge_user_id'] = $this->calendar->user_id;
    }
    $already_data = Ask::already_data($form);
    return $already_data;
  }
}
