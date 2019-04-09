<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarTag extends UserTag
{
  protected $table = 'user_calendar_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'calendar_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'calendar_id');
  }
  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($calendar_id, $tag_key, $tag_value , $create_user_id){
    UserCalendarTag::where('calendar_id', $calendar_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = UserCalendarTag::create([
        'calendar_id' => $calendar_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($calendar_id, $tag_key, $tag_values, $create_user_id){
    UserCalendarTag::where('calendar_id', $calendar_id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = UserCalendarTag::create([
        'calendar_id' => $calendar_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return UserCalendarTag::where('calendar_id', $calendar_id)->where('tag_key', $tag_key)->get();
  }

}
