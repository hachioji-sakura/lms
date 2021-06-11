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
  public static $id_name = 'user_calendar_setting_id';
  protected $guarded = array('id');
  public static $rules = array(
      'user_calendar_setting_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user_calendar_setting(){
    return $this->belongsTo('App\Models\UserCalendarSetting', 'user_calendar_setting_id');
  }
}
