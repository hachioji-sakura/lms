<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarMemberSetting extends Model
{
  protected $table = 'user_calendar_member_settings';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
  );
  public function setting(){
    return $this->belongsTo('App\Models\Calendar', 'user_calendar_setting_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }

}
