<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonRequestCalendar;

class LessonRequestCalendarController extends UserCalendarController
{
  public $domain = 'lesson_request_calendars';
  public $table = 'lesson_request_calendars';
  public function model(){
    return LessonRequestCalendar::query();
  }
  public function show_fields($item=''){
    $base_ret = [
      'datetime' => [
        'label' => __('labels.datetime'),
      ],
      'status_name' => [
        'label' => __('labels.status'),
        'size' => 6,
      ],
      'place_floor_name' => [
        'label' => __('labels.place'),
        'size' => 6,
      ],
    ];
    $ret = [
      'teacher_name' => [
        'label' => __('labels.teachers'),
        'size' => 6,
      ],
      'lesson' => [
        'label' => __('labels.lesson'),
        'size' => 6,
      ],
      'course' => [
        'label' => __('labels.lesson_type'),
        'size' => 6,
      ],
      'teaching_name' => [
        'label' => __('labels.lesson_name'),
        'size' => 6,
      ],
      'subject' => [
        'label' => __('labels.subject'),
        'size' => 6,
      ],
      'student_name' => [
        'label' => __('labels.students'),
        'size' => 12,
      ],
    ];
    $ret['remark'] = [
      'label' => __('labels.remark'),
      'size' => 12,
    ];
    $ret = array_merge($base_ret, $ret);
    return $ret;
  }
}
