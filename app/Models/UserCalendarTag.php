<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.user_calendar_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'calendar_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function scopeFindCalendar($query, $val)
  {
      return $query->where('calendar_id', $val);
  }
  public function scopeFindKey($query, $val)
  {
      return $query->where('tag_key', $val);
  }

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
    if(gettype($tag_values) == 'array'){
      foreach($tag_values as $tag_value){
        $item = UserCalendarTag::create([
          'calendar_id' => $calendar_id,
          'tag_key' => $tag_key,
          'tag_value' => $tag_value,
          'create_user_id' => $create_user_id,
        ]);
      }
    }
    else {
      $item = UserCalendarTag::create([
        'calendar_id' => $calendar_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_values,
        'create_user_id' => $create_user_id,
      ]);
    }
    return UserCalendarTag::where('calendar_id', $calendar_id)->where('tag_key', $tag_key)->get();
  }

}
