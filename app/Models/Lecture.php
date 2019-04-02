<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ChargeStudent;
use App\Models\GeneralAttribute;

class Lecture extends Model
{
  protected $table = 'lectures';
  protected $guarded = array('id');
  public static $rules = array(
      'lesson' => 'required',
      'course' => 'required',
      'subject' => 'required',
  );
  public function scopeFindChargeLesson($query, $teacher_id)
  {
    $where_raw = <<<EOT
      $this->table.lesson in (select tag_value from user_tags where user_id=(select user_id from teachers where id=? limit 1) and tag_key='lesson')
EOT;
    return $query->whereRaw($where_raw,[$teacher_id]);
  }
  public function scopeFindChargeStudentLesson($query, $teacher_id, $student_id)
  {
    $where_raw = <<<EOT
      $this->table.id in (select lecture_id from charge_students where teacher_id =? and student_id = ?)
EOT;
    return $query->whereRaw($where_raw,[$teacher_id, $student_id]);
  }
  public function details(){
    $item = [];
    $item['id'] = $this->id;
    $item['lesson'] = $this->_lesson();
    $item['lesson_name'] = $item['lesson']['attribute_name'];
    $item['course'] = $this->_course();
    $item['course_name'] = $item['course']['attribute_name'];
    $item['subject'] = $this->_subject();
    $item['subject_name'] = $item['subject']['attribute_name'];
    $item['name'] = $item['lesson']['attribute_name'].':'.$item['course']['attribute_name'].':'.$item['subject']['attribute_name'];
    return $item;
  }
  public function _lesson(){
    $item = GeneralAttribute::lesson($this->lesson)->first();
    return $item;
  }
  public function _subject(){
    $item = GeneralAttribute::subject($this->subject)->first();
    return $item;
  }
  public function _course(){
    $item = GeneralAttribute::course($this->course)->first();
    return $item;
  }
}
