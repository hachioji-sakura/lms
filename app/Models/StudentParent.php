<?php

namespace App\Models;
use App\User;
use App\Models\Student;
use App\Models\UserTag;
use App\Models\StudentParent;
use App\Models\StudentRelation;

use Illuminate\Database\Eloquent\Model;
class StudentParent extends Teacher
{
  protected $table = 'student_parents';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation', 'student_parent_id');
  }
  public function name()
  {
    $name = $this->name_last . ' ' .$this->name_first;
    if(empty(trim($name))){
      $name = $this->relation()->first()->student->name();
    }
    return $name;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  static public function entry($form){
    $ret = [];
    $parent_user = User::where('email', $form['email'])->first();
    $parent = null;
    if(isset($parent_user)){
      $parent = StudentParent::where('user_id', $parent_user->id)->first();
    }
    else {
      $parent_user = User::create([
          'name' => $form['name_last'].' '.$form['name_first'],
          'email' => $form['email'],
          'image_id' => 4,
          'status' => 1,
          'access_key' => $form['access_key'],
          'password' => '-',
      ]);
      $parent = StudentParent::create([
        'name_last' => $form['name_last'],
        'name_first' => $form['name_first'],
        'kana_last' => '',
        'kana_first' => '',
        'user_id' => $parent_user->id,
        'create_user_id' => 1,
      ]);
    }
    return $parent;
  }

  public function brother_add($form, $status=0){
    $ret = [];
    $student = null;
    foreach($this->relation() as $relation){
      $student = $relation->student;
      if($student->name_last ==$form['name_last'] &&
        $student->name_first == $form['name_first'] ){
          break;
      }
    }
    if(isset($student)){
      //すでに同姓同名の子供を登録済み
      return $student;
    }
    $form['create_user_id'] = $this->user_id;
    $form['status'] = $status;
    $student = Student::entry($form);
    StudentRelation::create([
      'student_id' => $student->id,
      'student_parent_id' => $this->id,
      'create_user_id' => $this->user_id,
    ]);
    $student->profile_update($form);
    return $student;
  }
  public function profile_update($form){
    $update_form = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'phone_no' => "",
      'address' => "",
    ];
    foreach($update_form as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);
    return $this;
  }
  public function relation(){
    $items = StudentRelation::where('student_parent_id', $this->id)->get();
    return $items;
  }
}
