<?php

namespace App\Models;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\Student;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $table = 'students';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
      'gender' => 'required',
      'birth_day' => 'required',
  );
  public function age(){
    return floor((date("Ymd") - str_replace("-", "", $this->birth_day))/10000);
  }
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  public function gender()
  {
    if($this->gender===1) return "男性";
    if($this->gender===2) return "女性";
    return "その他";
  }
  static protected function entry($form){
    $ret = [];
    $student_no = UserTag::where('tag_key', 'student_no')->max('tag_value');
    $student_no = intval(ltrim($student_no, '0'))+1;
    $student_no = sprintf('%06d', $student_no);
    $user = User::create([
      'name' => $form['name_last'].' '.$form['name_first'],
      'password' => '-',
      'email' => $student_no,
      'image_id' => $form['gender'],
      'status' => 1,
    ]);
    $student = Student::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
      'birth_day' => $form['birth_day'],
      'gender' => $form['gender'],
      'user_id' => $user->id,
      'create_user_id' => $user->id,
    ]);
    UserTag::create([
      'user_id' => $user->id,
      'tag_key' => 'student_no',
      'tag_value' => $student_no,
      'create_user_id' => $user->id,
    ]);
    return $student;
  }
  public function profile_update($form){
    $this->update([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
      'birth_day' => $form['birth_day'],
    ]);
    $tag_names = ['school_name', 'grade'];

    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
	      UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson_subject', 'lesson_week', 'lesson_place', 'lesson_time', 'lesson_time_holiday'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function parents(){
    $items = StudentRelation::where('student_id', $this->id)->get();
    $parents = [];
    foreach($items as $item){
      $parent = StudentParent::where('id', $item->student_parent_id)->first();
      $parents[] =User::where('id', $this->user_id)->first();
    }
    return $parents;
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation');
  }
}
