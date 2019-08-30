<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialStudent extends Model
{
  protected $table = 'lms.trial_students';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
  );
  public function trial(){
    return $this->belongsTo('App\Models\Trial', 'trial_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
