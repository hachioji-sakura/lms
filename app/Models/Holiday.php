<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
  protected $table = 'common.holidays';
  protected $guarded = array('id');

  public static $rules = array(
      'date' => 'required',
  );
  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeIsPublicPrivate($query, $is_public=true, $is_private=true)
  {
    $query = $query->where(function($query)use($is_public, $is_private){
        if($is_public==true){
          $query = $query->orWhere('is_public_holiday','1');
        }
        if($is_private==true){
          $query = $query->orWhere('is_private_holiday','1');
        }
    });
    return $query;
  }

  public function is_public_holiday(){
    if($this->is_public_holiday==1){
      return true;
    }
    return false;
  }
  public function is_private_holiday(){
    if($this->is_private_holiday==1){
      return true;
    }
    return false;
  }
}
