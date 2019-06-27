<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceFloorSheat extends Model
{
  protected $connection = 'mysql_common';
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

}
