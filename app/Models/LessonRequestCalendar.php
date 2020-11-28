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

}
