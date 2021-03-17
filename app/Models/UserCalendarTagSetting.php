<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCalendarTagSetting
 *
 * @property int $id
 * @property int $user_calendar_setting_id カレンダー設定ID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @property-read \App\Models\UserCalendarSetting $user_calendar_setting
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTagSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTagSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTagSetting query()
 * @mixin \Eloquent
 */
class UserCalendarTagSetting extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.user_calendar_tag_settings';
  protected $guarded = array('id');
  public static $rules = array(
      'user_calendar_setting_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user_calendar_setting(){
    return $this->belongsTo('App\Models\UserCalendarSetting', 'user_calendar_setting_id');
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
    if(gettype($tag_values) != "array"){
      return null;
    }
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
