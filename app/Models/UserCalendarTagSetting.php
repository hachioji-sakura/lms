<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarTagSetting extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.user_calendar_tag_settings';
  public static $id_name = 'user_calendar_setting_id';
  protected $guarded = array('id');
  public static $rules = array(
      'user_calendar_setting_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user_calendar_setting(){
    return $this->belongsTo('App\Models/UserCalendarSetting', 'user_calendar_setting_id');
  }
}
