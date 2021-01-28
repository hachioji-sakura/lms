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
  public function lesson_request_date(){
    return $this->belongsTo('App\Models\LessonRequestDate');
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
    //TODO :
    return null;
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){

    $calendar = LessonRequest::searchDate($form['start_time'], $form['end_time'])
      ->findStatuses(['rest', 'cancel', 'lecture_cancel'], true)
      ->where('user_id', $form['target_user_id'])->first();

    //TODO 重複登録、競合登録の防止が必要
    $calendar = UserCalendar::searchDate($form['start_time'], $form['end_time'])
      ->findStatuses(['rest', 'cancel', 'lecture_cancel'], true)
      ->where('user_id', $form['target_user_id'])->first();


    $calendar = LessonRequestCalendar::create([
      'start_time' => $form['start_time'],
      'end_time' => $form['end_time'],
      'lecture_id' => 0,
      'course_minutes' => $course_minutes,
      'trial_id' => $trial_id,
      'lesson_request_id' => $lesson_request_id,
      'user_calendar_setting_id' => $user_calendar_setting_id,
      'exchanged_calendar_id' => $form['exchanged_calendar_id'],
      'place_floor_id' => $form['place_floor_id'],
      'work' => $form['work'],
      'remark' => '',
      'user_id' => $form['target_user_id'],
      'create_user_id' => $form['create_user_id'],
      'status' => $status
    ]);
    $post = false;
    if($form['work']==9) $post = true;
    $calendar->memberAdd($form['target_user_id'], $form['create_user_id'], $status, $post);
    $is_sendmail = false;
    if(isset($form['send_mail']) && $form['send_mail'] == "teacher"){
      $is_sendmail = true;
      //新規登録時に変更メールを送らない
      unset($form['send_mail']);
    }
    $calendar->change($form);
    return $calendar->api_response(200, "", "", $calendar);
  }
  public function getStudentNameAttribute(){
    return $this->student->name;
  }
  public function subject(){
    $d = GeneralAttribute::where('attribute_key', 'charge_subject')->where('attribute_value', $this->subject_code)->first();
    if(!isset($d)) return "";
    return $d->attribute_name;
  }
}
