<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

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
}
