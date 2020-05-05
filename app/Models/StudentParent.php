<?php

namespace App\Models;
//データセット
use App\User;
use App\Models\Student;
use App\Models\Trial;
use App\Models\UserTag;
use App\Models\StudentParent;
use App\Models\StudentRelation;

use Illuminate\Database\Eloquent\Model;
class StudentParent extends Teacher
{
  protected $connection = 'mysql_common';
  protected $table = 'common.student_parents';
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
      $child = $this->relation()->first();
      if(isset($child)){
        $name = $child->student->name();
      }
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
      if(!isset($parent)) return null;
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
        'phone_no' => $form['phone_no'],
        'kana_last' => '',
        'kana_first' => '',
        'user_id' => $parent_user->id,
        'create_user_id' => 1,
        'status' => 'trial',
      ]);
    }
    return $parent;
  }

  public function brother_add($form, $status=0){
    $ret = [];
    $student = null;
    foreach($this->relation() as $relation){
      if($relation->student->name_last ==$form['name_last'] &&
        $relation->student->name_first == $form['name_first'] ){
          $student = $relation->student;
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
    $update_fields = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'phone_no' => "",
      'post_no' => "",
      'address' => "",
      'status' => "",
    ];
    $update_form = [];
    foreach($update_fields as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    if(!empty($form['parent_name_last']) && empty($form['name_last'])){
      $update_form['name_last'] = $form['parent_name_last'];
    }
    if(!empty($form['parent_name_first']) && empty($form['name_first'])){
      $update_form['name_first'] = $form['parent_name_first'];
    }
    if(!empty($form['parent_kana_last']) && empty($form['kana_last'])){
      $update_form['kana_last'] = $form['parent_kana_last'];
    }
    if(!empty($form['parent_kana_first']) && empty($form['kana_first'])){
      $update_form['kana_first'] = $form['parent_kana_first'];
    }

    $this->update($update_form);
    return $this;
  }
  public function relation(){
    $items = StudentRelation::where('student_parent_id', $this->id)->get();
    return $items;
  }
  public function get_enable_students(){
    $relations = $this->relation();
    $students = [];
    foreach($relations as $relation){
      if($relation->student->status=='regular'){
        $students[] = $relation->student;
      }
    }
    return $students;
  }
  public function details(){
    $item = $this;
    $students = [];
    //$item['relations'] = $this->relations;
    foreach($this->relation() as $relation){
      $students[] = $relation->student->details();
    }
    unset($item->user);
    $item['students'] = $students;
    $item['email'] = $this->user->email;

    return $item;
  }

}
