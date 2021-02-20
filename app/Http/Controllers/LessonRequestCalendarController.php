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

}
