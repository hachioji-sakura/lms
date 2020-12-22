<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class LessonRequestDate extends Model
{
  use Common;

  protected $connection = 'mysql';
  protected $table = 'lms.lesson_request_dates';
  public static $id_name = 'lesson_request_id';
  protected $guarded = array('id');
  protected $appends = ['term', 'month_day', 'from_datetime', 'to_datetime', 'from_hour', 'to_hour', 'created_date', 'updated_date'];
  public static $rules = array(
      'lesson_request_id' => 'required',
      'date' => 'required',
      'from_time_slot' => 'required',
      'to_time_slot' => 'required',
      'sort_no' => 'required',
  );
  public function lesson_request(){
    return $this->belongsTo('App\Models\LessonRequest');
  }
  public function getFromHourAttribute(){
    $h = explode(':', $this->from_time_slot);
    return intval($h[0]);
  }
  public function getToHourAttribute(){
    $h = explode(':', $this->to_time_slot);
    return intval($h[0]);
  }
  public function getFromDatetimeAttribute(){
    return $this->day.' '.$this->from_time_slot.':00';
  }
  public function getToDatetimeAttribute(){
    return $this->day.' '.$this->to_time_slot.':00';
  }
  public function getMonthDayAttribute(){
    return $this->dateweek_format($this->day);
  }
  public function getTermAttribute(){
    return $this->dateweek_format($this->day).$this->from_time_slot.'～'.$this->to_time_slot;
  }
  public function getTimezoneAttribute(){
    return $this->from_time_slot.'-'.$this->to_time_slot;
  }
}
