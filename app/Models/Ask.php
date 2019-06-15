<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\UserCalendarMember;

class Ask extends Milestone
{
  protected $table = 'asks';
  protected $guarded = array('id');

  public function charge_user(){
    return $this->belongsTo('App\User', 'charge_user_id');
  }
  public function scopeRangeDate($query, $from_date, $to_date=null, $field='start_time')
  {
    //日付検索
    if(!empty($from_date)){
      $query = $query->where($field, '>=', $from_date);
    }
    if(!empty($to_date)){
      $query = $query->where($field, '<', $to_date);
    }
    return $query;
  }
  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      (target_user_id = $user_id
      or create_user_id = $user_id
      or charge_user_id = $user_id
      )
EOT;
    return $query->whereRaw($where_raw,[1]);
  }
  public function scopeUser($query, $user_id)
  {
    return $query->where('charge_user_id', $user_id);
  }
  public function scopeChargeUser($query, $user_id)
  {
    return $query->where('charge_user_id', $user_id);
  }
  public function scopeRangeEndDate($query, $from_date, $to_date=null, $field='start_time')
  {
    return $this->scopeRangeDate($query, $from_date, $to_date, 'end_time');
  }
  public function scopeSortEnddate($query, $sort){
    if(empty($sort)) $sort = 'asc';
    return $query->orderBy('end_date', $sort);
  }
  static protected function add($type, $form){
    $type_template = [
      "rest_cancel" => [
        "title" => "休み取り消し依頼",
      ],
      "presence_check" => [
        "title" => "出欠確認依頼",
      ],
      "lecture_cancel" => [
        "title" => "休講依頼",
      ],
    ];
    $title = $type_template[$type]["title"];
    if(!isset($form['title'])){
      $form['title'] = $title;
    }
    if(!isset($form['start_date'])){
      $form['start_date'] = date('Y-m-d');
    }
    if(!isset($form['body'])){
      $form['body'] = '';
    }
    if(!isset($form['target_model'])){
      $form['target_model'] = '';
    }
    if(!isset($form['target_model_id'])){
      $form['target_model_id'] = 0;
    }
    //重複チェック
    $ask = Ask::where('type', $type)
      ->where('status', '!=', 'cancel')
      ->where('target_model', $form['target_model'])
      ->where('target_model_id', $form['target_model_id'])
      ->where('target_user_id', $form['target_user_id'])->get();
    if(count($ask)>0) {
      //重複登録
      return null;
    }
    $ask = Ask::create([
      'start_date' => $form['start_date'],
      'end_date' => $form['end_date'],
      'type' => $type,
      'status' => 'new',
      'title' => $form['title'],
      'body' => $form['body'],
      'target_model' => $form['target_model'],
      'target_model_id' => $form['target_model_id'],
      'charge_user_id' => $form['charge_user_id'],
      'target_user_id' => $form['target_user_id'],
      'create_user_id' => $form['create_user_id'],
    ]);
    if($ask->is_auto_commit()==true){
      //自動承認対象
      \Log::warning("自動承実行");
      $ask = $ask->change(['status'=>'commit', 'login_user_id'=>$ask->create_user_id]);
    }
    else {
      $ask->remind_mail($form['create_user_id']);
    }
    return $ask;
  }
  public function change($form){
    if(isset($form["status"]) && isset($form["login_user_id"])){
      $this->update(['status'=>$form['status']]);
      $this->_change($form['status'], $form['login_user_id']);
    }
    return $this;
  }
  public function end_dateweek(){
    $d = date('n月j日',  strtotime($this->end_date));
    $d .= '('.config('week')[date('w',  strtotime($this->end_date))].')';
    return $d;
  }
  public function type_name()
  {
    return $this->config_attribute_name('ask_type', $this->type);
  }
  public function status_name()
  {
    return $this->config_attribute_name('ask_status', $this->status);
  }
  public function details(){
    $item = $this;
    $item["type_name"] = $this->type_name();
    $item["status_name"] = $this->status_name();
    if($this->charge_user_id==1) $item["charge_user_name"] = "事務";
    else $item["charge_user_name"] = $this->charge_user->details()->name();
    $item["create_user_name"] = $this->create_user->details()->name();
    $item["target_user_name"] = $this->target_user->details()->name();
    $item["end_dateweek"] = $this->end_dateweek();
    return $item;
  }
  public function _change($status, $login_user_id){
    $ret = false;
    $is_commit = false;
    if($status == 'commit'){
      $is_commit = true;
    }
    switch($this->type){
      case "rest_cancel":
      case "lecture_cancel":
        $member = UserCalendarMember::where('id', $this->target_model_id)->first();
        if(!isset($member)){
          return false;
        }
        $ret = true;
        if($this->type=='rest_cancel') $member->rest_cancel($is_commit, $login_user_id);
        if($this->type=='lecture_cancel') $member->lecture_cancel($is_commit, $login_user_id);
        break;
    }
    return $ret;
  }
  //依頼に関する通知
  public function remind_mail($login_user_id, $is_remind=false){
    $res = false;
    $param = [];
    $param['send_to'] = 'teacher';
    $param['login_user'] = User::where('id', $login_user_id)->first()->details();
    switch($this->type){
      case "rest_cancel":
      case "lecture_cancel":
        $member = UserCalendarMember::where('id', $this->target_model_id)->first();
        if(!isset($member)){
          return false;
        }
        $param["item"] = $member->calendar->details($this->target_user_id);
        break;
    }
    //依頼対象者にメール通知
    $res = $this->target_user_mail($param);
    $res = $this->charge_user_mail($param);
    if($this->type=='lecture_cancel' && $this->status='commit'){
      //生徒あてに休講連絡
      $param['send_to'] = 'student';
      $param["item"]->student_mail('休講のお知らせ', $param, 'text', 'calendar_lecture_cancel');
    }
    return $res;
  }
  public function target_user_mail($param){
    if($this->target_user_id==1) return false;
    $title = $this->type_name().':'.$this->status_name();
    \Log::info("target_user_mail=".$title);
    $param['send_to'] = 'teacher';
    if($this->target_user->details('students')->role=='student'){
      $param['send_to'] = 'student';
    }
    return $this->send_mail($this->target_user_id, $title, $param, 'text', 'ask_'.$this->type.'_'.$this->status);
  }
  public function charge_user_mail($param){
    if($this->charge_user_id==1) return false;
    $title = $this->type_name().':'.$this->status_name();
    $param['send_to'] = 'teacher';
    if($this->charge_user->details('students')->role=='student'){
      $param['send_to'] = 'student';
    }
    return $this->send_mail($this->charge_user_id, $title, $param, 'text', 'ask_'.$this->type.'_'.$this->status);
  }
  public function is_auto_commit(){
    \Log::warning("休講自動承認：ask[".$this->id."][".$this->type."]");
    $ret = false;
    switch($this->type){
      case "rest_canel":
        break;
      case "lecture_cancel":
        $check = [];
        if($this->target_model=="user_calendar_members" && $this->target_model_id>0){
          $m = UserCalendarMember::where('id', $this->target_model_id)->first();
          if(!isset($m)) return false;
          if($m->calendar->is_group()==true && $m->calendar->is_english_talk_lesson()==true) return false;
          \Log::warning("休講自動承認：calendar[".$m->calendar->id."]");
          $d = date('Ymd',  strtotime($m->calendar->start_time));
          $check[$d] = true;
        }
        \Log::warning("依頼は2日まで");
        $asks = Ask::where('type', 'lecture_cancel')
                  ->where('target_user_id', $this->target_user_id)
                  ->where('status', '=', 'commit')
                  ->where('end_date', '>=', date('Y-m-1'))
                  ->where('end_date', '<=', date('Y-m-t'))
                  ->get();
        $c = 0;
        //今月承認された休講が3日以上ある場合、自動承認できない
        foreach($asks as $ask){
          $m = UserCalendarMember::where('id', $ask->target_model_id)->first();
          $d = date('Ymd',  strtotime($m->calendar->start_time));
          $check[$d] = true;
        }
        \Log::warning("休講自動承認：ask.count[".count($check)."]");
        if(count($check) > 2) return false;
        $ret = true;
        break;
    }
    \Log::warning("休講自動承認：[".$ret."]");
    return $ret;
  }
}
