<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserCalendar;

class UserCalendar extends Model
{
  protected $table = 'user_calendars';
  protected $guarded = array('id');
  public static $rules = array(
      'lecture_id' => 'required',
      'start_time' => 'required',
      'end_time' => 'required'
  );
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMember', 'calendar_id');
  }
  public function place(){
    $item = GeneralAttribute::place($this->place)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
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
    }
    return "";
  }

}
