<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolGrade extends Milestone
{
  protected $table = 'lms.school_grades';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  protected $appends = ['type_name', 'create_user_name', 'target_user_name', 'importance_label', 'publiced_date', 'created_date', 'updated_date'];

  public function reports(){
    return $this->hasMany('App\Models\SchoolGradeReport');
  }

}
