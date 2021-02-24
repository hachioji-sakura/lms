<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;
use App\Models\GeneralAttribute;

class LessonRequestCalendar extends UserCalendar
{
  use Common;
  protected $table = 'lms.lesson_request_calendars';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'start_time' => 'required',
      'end_time' => 'required'
  );
  protected $appends = ['work', 'create_user_name', 'target_user_name', 'created_date', 'updated_date'];

  public function lesson_request_date(){
    return $this->belongsTo('App\Models\LessonRequestDate');
  }
  public function traing_calendars(){
    return $this->hasMany('App\Models\LessonRequestCalendar', 'parent_lesson_request_calendar_id');
  }
  public function lesson_request(){
    return $this->lesson_request_date->lesson_request();
  }
  public function student(){
    return $this->lesson_request_date->lesson_request->student();
  }
  public function prev_calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'prev_calendar_id');
  }
  public function next_calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'next_calendar_id');
  }
  public function getDurationAttribute(){
    return date('H:i', strtotime($this->start_time)).'～'.date('H:i', strtotime($this->end_time));
  }
  public function scopeSearchLessonRequest($query, $lesson_request_id){
    if(empty($lesson_request_id)) return $query;
    return $query->whereHas('lesson_request_date', function($query) use ($lesson_request_id) {
        $query = $query->where('lesson_request_id', $lesson_request_id);
    });
  }
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.request_calendar_status')[$this->status])){
      $status_name = config('attribute.request_calendar_status')[$this->status];
    }
    return $status_name;
  }
  public function tags(){
    return $this->hasMany('App\Models\LessonRequestCalendarTag');
  }
  public function getStudentNameAttribute(){
    return $this->student->name;
  }
  public function subject(){
    $d = GeneralAttribute::where('attribute_key', 'charge_subject')->where('attribute_value', $this->subject_code)->first();
    if(!isset($d)) return "";
    return $d->attribute_name;
  }
  public function is_charge_teacher(){
    $teachers = $this->student->get_current_charge_teachers();
    if(isset($teachers[$this->user_id])) return true;
    return false;
  }
  public function details($user_id=0){
    return $this;
  }
  public function is_teaching(){
    return "";
  }
  public function is_group(){
    return false;
  }
  public function work(){
    return "";
  }
  public function is_teacher_place_enabled(){
    $enable_places = $this->user->teacher->enable_places('season_lesson');
    if(isset($enable_places[$this->place_floor->place->id])) return true;
    return false;
  }
}
