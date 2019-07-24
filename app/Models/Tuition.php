<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuition extends Model
{
  protected $table = 'common.students';
  protected $guarded = array('id');
  /**
   * 入力ルール
   */
  public static $rules = array(
      'student_id' => 'required',
      'lesson' => 'required',
      'kana_last' => 'required',
      'course_type' => 'required',
      'lesson_week_count' => 'required',
      'tution' => 'required',
  );

  /**
   *　リレーション：生徒
   */
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  /**
   *　リレーション：講師
   */
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher', 'teacher_id');
  }
  /**
   *　授業形態：名称
   */
  public function course_type(){
    if(!isset($this->course_type)) return "";
    return $this->get_attribute_name('course_type', $this->course_type);
  }
  /**
   * レッスン：名称
   */
  public function lesson(){
    if(!isset($this->lesson)) return "";
    return $this->get_attribute_name('lesson', $this->lesson);
  }
  /**
   * 習い事：名称
   */
  public function kids_lesson(){
    if(!isset($this->kids_lesson)) return "";
    return $this->get_attribute_name('kids_lesson', $this->lesson);
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){
    $tuition = Tuition::create([
      'student_id' => $form['student_id'],
      'teacher_id' => $form['teacher_id'],
      'tuition' => $form['tuition'],
      'title' => $form['title'],
      'body' => $form['body'],
      'lesson' => $form['lesson'],
      'course_type' => $form['course_type'],
      'lesson_week_count' => $form['lesson_week_count'],
      'kids_lesson' => $form['kids_lesson'],
    ]);
    return $tuition;
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    $this->delete();
  }
  //本モデルはupdateではなくchangeを使う
  public function change($form){
    $_form = [];
    $_fields = ['start_date', 'end_date', 'tuition', 'title', 'body'];
    foreach($_fields as $field){
      if(isset($form[$field])){
        $_form[$field] = $form[$field];
      }
    }
    $this->update($_form);
    return $this;
  }
}
