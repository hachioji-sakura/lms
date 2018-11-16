<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
  protected $table = 'courses';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required',
      'lesson_id' => 'required',
      'subject_id' => 'required',
  );
/*
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  public function user(){
    return $this->hasOne('App\User');
  }
*/
}
