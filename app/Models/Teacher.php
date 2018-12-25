<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
  protected $table = 'teachers';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required',
      'kana' => 'required',
  );

  public function name()
  {
      return $this->name;
  }
  public function kana()
  {
      return $this->kana;
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function chargeStudents(){
    return $this->hasMany('App\Models\ChargeStudent');
  }
}
