<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuition extends Model
{
  protected $connection = 'mysql';
  protected $table = 'lms.tuitions';
  protected $guarded = array('id');
  /**
   * 入力ルール
   */
  public static $rules = array(
      'student_id' => 'required',
      'teacher_id' => 'required',
      'lesson' => 'required',
      'course_type' => 'required',
      'course_minutes' => 'required',
      'lesson_week_count' => 'required',
      'tution' => 'required',
  );

  /**
   *　リレーション：登録者
   */
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
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
   *　学年：名称
   */
  public function grade(){
    if(empty($this->grade)) return "";
    return $this->get_attribute_name('grade', $this->grade);
  }
  /**
   *　授業形態：名称
   */
  public function course_type(){
    if(empty($this->course_type)) return "";
    return $this->get_attribute_name('course_type', $this->course_type);
  }
  /**
   *　授業時間：名称
   */
  public function course_minutes(){
    if(empty($this->course_minutes)) return "";
    return $this->get_attribute_name('course_minutes', $this->course_minutes);
  }
  /**
   * レッスン：名称
   */
  public function lesson(){
    if(empty($this->lesson)) return "";
    return $this->get_attribute_name('lesson', $this->lesson);
  }
  /**
   * 科目
   */
  public function subject(){
    if(empty($this->subject)) return "";
    if($this->lesson==2){
      return $this->get_attribute_name('english_talk_lesson', $this->subject);
    }
    else if($this->lesson==4){
      return $this->get_attribute_name('kids_lesson', $this->subject);
    }
    return "";
  }
  public function get_attribute_name($key, $val){
    $item = GeneralAttribute::findKeyValue($key,$val)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  public function is_enable(){
    $s = strtotime($this->start_date.' 00:00:00');
    $e = strtotime($this->end_date.' 00:00:00');
    $now = strtotime(date('Y/m/d H:i:s'));
    if($s < $now && $e > $now) return true;
    return false;
  }
  public function enable_date(){
    $start_date = '';
    $end_date = '';
    if($this->start_date != '9999-12-31') $start_date = date('Y/m/d', strtotime($this->start_date));
    if($this->end_date != '9999-12-31') $end_date = date('Y/m/d', strtotime($this->end_date));
    if(empty($start_date) && empty($end_date)) return '-';
    return $start_date.'～'.$end_date;
  }
  public function details(){
    $item = $this;
    $item["lesson_name"] = $this->lesson();
    if($this->lesson==4 || $this->lesson==2){
      $item["lesson_name"].=":".$this->subject();
    }
    $item["course_type_name"] = $this->course_type();
    $item["course_minutes_name"] = $this->course_minutes();
    $item["grade_name"] = $this->grade();
    $item["teacher_name"] = $this->teacher->name();
    $item["student_name"] = $this->student->name();
    $item["tuition_money"] = "￥".number_format(intval($item["tuition"]),0);
    $item["enable_date"] = $this->enable_date();
    return $item;
  }

  //本モデルはcreateではなくaddを使う
  static protected function add($form){
    $already_data = Tuition::where('student_id' , $form['student_id'])
    ->where('teacher_id' , $form['teacher_id'])
    ->where('lesson' , $form['lesson'])
    ->where('course_type' , $form['course_type'])
    ->where('course_minutes' , $form['course_minutes'])
    ->where('grade' , $form['grade'])
    ->where('lesson_week_count' , $form['lesson_week_count'])
    ->where('subject' , $form['subject'])->get();
    if(isset($already_data) && count($already_data)>0){
      \Log::warning("tuition : already");
      return null;
    }
    $tuition = Tuition::create([
      'student_id' => $form['student_id'],
      'teacher_id' => $form['teacher_id'],
      'tuition' => $form['tuition'],
      'title' => $form['title'],
      'remark' => $form['remark'],
      'lesson' => $form['lesson'],
      'course_type' => $form['course_type'],
      'course_minutes' => $form['course_minutes'],
      'grade' => $form['grade'],
      'lesson_week_count' => $form['lesson_week_count'],
      'subject' => $form['subject'],
      'create_user_id' => $form['create_user_id'],
      'start_date' => $form['start_date'],
      'end_date' => $form['end_date'],
    ]);
    \Log::warning("tuition : insert");
    return $tuition;
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    $this->delete();
  }
  //本モデルはupdateではなくchangeを使う
  public function change($form){
    $_form = [];
    $_fields = ['start_date', 'end_date', 'tuition', 'remark'];
    foreach($_fields as $field){
      if(isset($form[$field])){
        $_form[$field] = $form[$field];
      }
    }
    $this->update($_form);
    return $this;
  }
}
