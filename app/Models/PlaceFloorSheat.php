<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
