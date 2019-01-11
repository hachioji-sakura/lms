<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarMember extends Model
{
  protected $table = 'user_calendar_members';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
  );
  public function calendar(){
    return $this->belongsTo('App\Models\Calendar', 'calendar_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function status_name(){
    $status_name = "";
    switch($this->status){
      case "new":
        return "仮登録";
      case "cancel":
        return "キャンセル";
      case "fix":
        return "確定";
      case "check":
        return "確認済み";
    }
    return "";
  }

}
