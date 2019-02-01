<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\UserCalendar;

use Illuminate\Database\Eloquent\Model;

class ChargeStudent extends Model
{
  protected $table = 'charge_students';
  protected $guarded = array('id');

  public static $rules = array(
      'student_id' => 'required',
      'teacher_id' => 'required',
      'lecture_id' => 'required',
  );
  public function scopeLikeStudentName($query, $search_word)
  {
    if(empty($search_word)) return $query;
    $where_raw = '';
    $words = explode(' ', $search_word);
    foreach($words as $word){
      if(empty($word)) continue;
      $_like = "'%".$word."%'";
      $where_raw .= 'OR name_last like '.$_like;
      $where_raw .= 'OR name_first like '.$_like;
      $where_raw .= 'OR kana_last like '.$_like;
      $where_raw .= 'OR kana_first like '.$_like;
    }
    $where_raw = $this->table.'.student_id in (select id from students where '.trim($where_raw, 'OR ').')';
    return $query->whereRaw($where_raw,[]);
  }
  public function scopeFindTeacher($query, $val)
  {
      return $query->where('teacher_id', $val);
  }
  public function scopeFindStudent($query, $val)
  {
      return $query->where('student_id', $val);
  }
  public function current_calendar()
  {
    $student = Student::where('id', $this->student_id)->first();
    $teacher = Teacher::where('id', $this->teacher_id)->first();
    $calendar = UserCalendar::findUser($student->user_id)
      ->findUser($teacher->user_id)->rangeDate(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'))
      ->orderBy('start_time')->first();
    return $calendar;
  }
  public function student(){
    return $this->belongsTo('App\Models\Student');
  }
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher');
  }
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
}
