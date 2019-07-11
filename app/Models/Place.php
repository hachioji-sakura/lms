<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
  protected $connection = 'mysql_common';
  protected $table = 'common.places';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required',
  );
  public function scopeFindId($query, $val)
  {
      return $query->where('id', $val);
  }
  /**
   *　リレーション：フロア
   */
  public function floors(){
    return $this->hasMany('App\Models\PlaceFloor', 'place_id');
  }
  public function name(){
    if(app()->getLocale()=='en') return $this->name_en;
    return $this->name;
  }
}
