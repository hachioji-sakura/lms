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
    if(!isset($form['target_id'])){
      $form['target_id'] = 0;
    }
    $ask = Ask::create([
      'start_date' => $form['start_date'],
      'end_date' => $form['end_date'],
      'type' => $type,
      'status' => 'new',
      'title' => $form['title'],
      'body' => $form['body'],
      'target_model' => $form['target_model'],
      'target_id' => $form['target_id'],
      'charge_user_id' => $form['charge_user_id'],
      'target_user_id' => $form['target_user_id'],
      'create_user_id' => $form['create_user_id'],
    ]);
    return $ask;
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
}
