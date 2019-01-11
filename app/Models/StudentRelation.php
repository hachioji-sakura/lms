<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRelation extends Model
{
  protected $table = 'student_relations';
  protected $guarded = array('id');

  public static $rules = array(
      'student_id' => 'required',
      'student_parent_id' => 'required',
  );
  public function student(){
    return $this->belongsTo('App\Models\Student');
  }
  public function parent(){
    return $this->belongsTo('App\Models\StudentParent');
  }
}
