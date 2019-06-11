<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
      ]
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
    return $ask;
  }
  public function change($form){
    if(isset($form["status"])){
      $this->update(['status'=>$form['status']]);
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
    $item["charge_user_name"] = $this->charge_user->details()->name();
    $item["create_user_name"] = $this->create_user->details()->name();
    $item["target_user_name"] = $this->target_user->details()->name();
    $item["end_dateweek"] = $this->end_dateweek();
    return $item;
  }
  public function commit(){
    if($this->status!="commit") return false;
    switch($this->type){
      case "rest_cancel":
        $member = UserCalendarMember::where('id', $this->target_model_id)->first();
        if(!isset($member)){
          return false;
        }
        $member->update(['status' => 'fix']);
        $member->calendar->update(['status' => 'fix']);
        break;
    }
  }
  public function cancel(){
    if($this->status!="cancel") return false;
    switch($this->type){
      case "rest_cancel":
        $member = UserCalendarMember::where('id', $this->target_model_id)->first();
        if(!isset($member)){
          return false;
        }
        $member->update(['status' => 'rest']);
        $member->calendar->change(['status' => 'rest']);
        break;
    }
    return true;
  }
  public function remind_mail($param, $is_remind=false){
    $res = false;
    switch($this->type){
      case "rest_cancel":
        $member = UserCalendarMember::where('id', $this->target_model_id)->first();
        if(!isset($member)){
          return false;
        }
        $param["item"] = $member->calendar->details($this->target_user_id);
        if($this->status=="commit") $res = $this->commit();
        else if($this->status=="cancel") $res = $this->cancel();

        $res = $this->target_user_mail($param);
        break;
    }
    return $res;
  }
  public function target_user_mail($param){
    $title = $this->type_name().':'.$this->status_name();
    \Log::info("target_user_mail=".$title);
    return $this->send_mail($this->target_user_id, $title, $param, 'text', 'ask_'.$this->type.'_'.$this->status);
  }
  public function charge_user_mail($param){
    $title = $this->type_name().':'.$this->status_name();
    return $this->send_mail($this->charge_user_id, $title, $param, 'text', 'ask_'.$this->type.'_'.$this->status);
  }
}
