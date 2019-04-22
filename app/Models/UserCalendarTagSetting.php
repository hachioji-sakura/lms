<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarTagSetting extends UserTag
{
  protected $table = 'user_calendar_tag_settings';
  protected $guarded = array('id');
  public static $rules = array(
      'user_calendar_setting_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user_calendar_setting(){
    return $this->belongsTo('App\Models/UserCalendarSetting', 'user_calendar_setting_id');
  }
  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($user_calendar_setting_id, $tag_key, $tag_value , $create_user_id){
    UserCalendarTagSetting::where('user_calendar_setting_id', $user_calendar_setting_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = UserCalendarTagSetting::create([
        'user_calendar_setting_id' => $user_calendar_setting_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($user_calendar_setting_id, $tag_key, $tag_values, $create_user_id){
    UserCalendarTagSetting::where('user_calendar_setting_id', $user_calendar_setting_id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = UserCalendarTagSetting::create([
        'user_calendar_setting_id' => $user_calendar_setting_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return UserCalendarTagSetting::where('user_calendar_setting_id', $user_calendar_setting_id)->where('tag_key', $tag_key)->get();
  }
}
