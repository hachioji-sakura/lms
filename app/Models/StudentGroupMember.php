<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGroupMember extends Model
{
  protected $table = 'student_group_members';
  protected $guarded = array('id');
  public static $rules = array(
    'student_group_id' => 'required',
    'student_id' => 'required',
  );
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  public function student_group(){
    return $this->belongsTo('App\Models\StudentGroup', 'student_group_id');
  }
  public function is_family(){
    $relations = StudentRelation::where('student_id', $this->student_id)->get();
    foreach($relations as $relation){
    }
  }
}
