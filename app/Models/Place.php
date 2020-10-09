<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class Place extends Model
{
  use Common;
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
  public function is_use(){
    foreach($this->floors as $floor){
      if($floor->is_use==true) return true;
    }
    return false;
  }
  public function getIsUseAttribute(){
    if($this->is_use()==true) return '使用中';
    return '未使用';
  }

  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name','like', $_like)
          ->orWhere('address','like', $_like)
          ->orWhere('name_en','like', $_like);
      }
    });
    return $query;
  }
  public function dispose(){
    if($this->is_use()==true){
      return $this->error_response('このデータはカレンダーにて使用されており削除できません');
    }
    foreach($this->floors as $floor){
      PlaceFloorSheat::where('place_floor_id', $floor->id)->delete();
    }
    PlaceFloor::where('place_id', $this->id)->delete();
    $this->delete();
  }
}
