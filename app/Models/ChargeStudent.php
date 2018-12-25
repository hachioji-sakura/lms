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
      'lecture_id' => 'required',
  );
  public function student(){
    return $this->belongsTo('App\Models\Student');
  }
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher');
  }
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
}
