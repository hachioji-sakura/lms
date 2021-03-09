<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PlaceFloorSheat
 *
 * @property int $id
 * @property int $place_floor_id フロアID
 * @property string $name 席名
 * @property int $sort_no 表示順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserCalendarMember[] $calendar_members
 * @property-read \App\Models\PlaceFloor $floor
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloorSheat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloorSheat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloorSheat query()
 * @mixin \Eloquent
 */
class PlaceFloorSheat extends Model
{
  protected $table = 'common.place_floor_sheats';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required',
  );
  /**
   *　リレーション：所在地
   */
  public function floor(){
    return $this->belongsTo('App\Models\PlaceFloor', 'place_floor_id');
  }
  public function calendar_members(){
    return $this->hasMany('App\Models\UserCalendarMember', 'place_floor_sheat_id');
  }

  public function is_use(){
    $c = count($this->calendar_members);
    if($c > 0) return true;
    return false;
  }
}
