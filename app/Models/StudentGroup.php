<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentGroupMember;
class StudentGroup extends Model
{
  protected $pagenation_line = 20;
  protected $table = 'common.student_groups';
  protected $guarded = array('id');
  public static $rules = array(
    'teacher_id' => 'required',
    'title' => 'required',
    'type' => 'required',
  );
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher', 'teacher_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function members(){
    return $this->hasMany('App\Models\StudentGroupMember', 'student_group_id');
  }
  public function type_name(){
    $g = GeneralAttribute::get_item('course_type', $this->type);
    if(isset($g)) return $g["attribute_name"];
    return "";
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){
    $student_group = StudentGroup::create([
      "title" => $form["title"],
      "type" => $form["type"],
      "teacher_id" => $form["teacher_id"],
      'create_user_id' => $form['create_user_id'],
    ]);
    $student_group->change($form);
    return $student_group;
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    StudentGroupMember::where('student_group_id', $this->id)->delete();
    $this->delete();
  }
  //本モデルはupdateではなくchangeを使う
  public function change($form){
    $update_fields = [
      'title',
      'teacher_id',
      'type',
      'remark',
    ];
    $data = [
      "title" => $this->title,
      "teacher_id" => $this->teacher_id
    ];
    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      if($field=='title' && empty($form[$field])) continue;
      if($field=='teacher_id' && empty($form[$field])) continue;
      $data[$field] = $form[$field];
    }
    $this->update($data);
    StudentGroupMember::where('student_group_id', $this->id)->delete();
    foreach($form['student_id'] as $student_id){
      StudentGroupMember::create([
        'student_group_id' => $this->id,
        'student_id' => $student_id,
        'create_user_id' => $form['create_user_id'],
      ]);
    }
    return $this;
  }
  public function is_member($student_id){
    foreach($this->members as $member){
      if($member->student_id === $student_id){
        return true;
      }
    }
    return false;
  }
  public function is_members($student_ids){
    foreach($student_ids as $student_id){
      //1人でもメンバーでない場合=false
      if($this->is_member($student_id)===false) return false;
    }
    return true;
  }
  public function is_family($student){
    foreach($members as $member){
      if($member->student->is_family($student)==false){
        //一人でも家族でない人がいる
        return false;
      }
    }
    return true;
  }
  public function details($user_id=0){
    $item = $this;
    $student_name = "";
    $students = [];
    foreach($this->members as $member){
      if(!isset($member->student)) continue;
      $student_name.=$member->student->name().',';
      $students[] = $member->student;
    }
    $item['students'] = $students;
    $item['type_name'] = $this->type_name();;
    $item['student_name'] = trim($student_name,',');
    if($this->teacher){
      $item['teacher_name'] = $this->teacher->name();
    }
    else {
      $item['teacher_name'] = $this->teacher_id;
    }
    return $item;
  }

}
