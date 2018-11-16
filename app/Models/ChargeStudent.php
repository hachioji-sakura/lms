<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeStudent extends Model
{
  protected $table = 'charge_students';
  protected $guarded = array('id');

  public static $rules = array(
      'student_id' => 'required',
      'teacher_id' => 'required',
      'course_id' => 'required',
  );
  public function student(){
    return $this->hasOne('App\Models\Student');
  }
  public function teacher(){
    return $this->hasOne('App\Models\Teacher');
  }
  public function course(){
    return $this->hasOne('App\Models\Course');
  }
}
