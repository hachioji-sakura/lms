<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonRequestCalendarTag extends Model
{
  protected $table = 'lms.lesson_request_calendar_tags';
  public static $id_name = 'lesson_request_calendar_id';
  protected $guarded = array('id');
  public static $rules = array(
      'lesson_request_calendar_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function scopeFindCalendar($query, $val)
  {
      return $query->where('lesson_request_calendar_id', $val);
  }
  public function calendar(){
    return $this->belongsTo('App\Models\LessonRequestCalendar', 'lesson_request_calendar_id');
  }

}
