<?php

namespace App\Models;
use App\User;
use App\Models\Student;
use App\Models\UserTag;
use App\Models\StudentParent;
use App\Models\StudentRelation;

use Illuminate\Database\Eloquent\Model;
class StudentParent extends Model
{
  protected $table = 'student_parents';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
  );
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  static protected function entry($form){
    $ret = [];
    $parent_user = User::create([
        'name' => $form['name_last'].' '.$form['name_first'].' 保護者様',
        'email' => $form['email'],
        'image_id' => 4,
        'status' => 1,
        'access_key' => $form['access_key'],
        'password' => $form['password'],
    ]);
    $parent = StudentParent::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'].' 保護者様',
      'kana_last' => '',
      'kana_first' => '',
      'user_id' => $parent_user->id,
      'create_user_id' => 1,
    ]);
    $student_no = UserTag::where('tag_key', 'student_no')->max('tag_value');
    $student_no = intval(ltrim($student_no, '0'))+1;
    $student_no = sprintf('%06d', $student_no);
    $user = User::create([
      'name' => $form['name_last'].' '.$form['name_first'],
      'password' => $form['password'],
      'email' => $student_no,
      'image_id' => $form['gender'],
      'status' => 1,
    ]);
    $student = Student::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => '',
      'kana_first' => '',
      'birth_day' => '9999-12-31',
      'gender' => $form['gender'],
      'user_id' => $user->id,
      'create_user_id' => 1,
    ]);
    StudentRelation::create([
      'student_id' => $student->id,
      'student_parent_id' => $parent->id,
      'create_user_id' => 1,
    ]);
    UserTag::create([
      'user_id' => $user->id,
      'tag_key' => 'student_no',
      'tag_value' => $student_no,
      'create_user_id' => 1,
    ]);
    $ret['parent'] = $parent;
    $ret['parent']['user'] = $parent_user;
    $ret['student'] = $student;
    $ret['student']['user'] = $user;
    $ret['student']['student_no'] = $student_no;
    return $ret;
  }

  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation');
  }
  public function childs(){
    $items = StudentRelation::where('student_parent_id', $this->id)->get();
    $childs = [];
    foreach($items as $item){
      $child = Student::where('id', $item->student_id)->first();
      $childs[] =$child;
    }
    return $childs;
  }
}
