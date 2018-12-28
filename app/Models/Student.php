<?php

namespace App\Models;
use App\Models\Image;
use App\Models\chargeStudent;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $table = 'students';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
      'gender' => 'required',
      'birth_day' => 'required',
  );
  public function age(){
    return floor((date("Ymd") - str_replace("-", "", $this->birth_day))/10000);
  }
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  public function gender()
  {
    if($this->gender===1) return "男性";
    if($this->gender===2) return "女性";
    return "その他";
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }

}
