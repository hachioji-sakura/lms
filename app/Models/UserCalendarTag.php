<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCalendarTag
 *
 * @property int $id
 * @property int $calendar_id カレンダーID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserCalendar $calendar
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTag findCalendar($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCalendarTag query()
 * @mixin \Eloquent
 */
class UserCalendarTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.user_calendar_tags';
  public static $id_name = 'calendar_id';
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
  public function calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'calendar_id');
  }


}
