<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;
use App\Models\UserCalendar;

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
  public function calendars(){
    return $this->hasMany('App\Models\LessonRequestCalendar');
  }
  public function getFromHourAttribute(){
    $h = explode(':', $this->from_time_slot);
    return intval($h[0]);
  }
  public function getToHourAttribute(){
    $h = explode(':', $this->to_time_slot);
    return intval($h[0]);
  }
  public function scopeSearchEvent($query, $event_id){
    if(empty($event_id)) return $query;
    return $query->whereHas('lesson_request', function($query) use ($event_id) {
        $query = $query->where('event_id', $event_id);
    });
  }
  public function scopeSearchUser($query, $user_id)
  {
    if(empty($user_id)) return $query;
    return $query->whereHas('lesson_request', function($query) use ($user_id) {
        $query = $query->where('user_id', $user_id);
    });
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
  public function add_calendar(){
    //1.対象ユーザーの予定がすでに登録されているかチェック
    $c = UserCalendar::findUser($this->lesson_request->user_id)
        ->where('start_time', '>=', $this->day.' '.$this->from_time_slot)
        ->where('start_time', '<=', $this->day.' '.$this->to_time_slot)
        ->findStatuses(['rest', 'cancel', 'lecture_cancel'], true)
        ->get();
    if($this->lesson_request->is_hope_exchange()==true){
      if(isset($c) && count($c)==1){
        //1-1. 登録済みかつ、通常授業→講習への振替希望の場合
        //予定が1件の場合

      }
      else {
        //振替元が一意にできない

      }
    }
    else {
      //1-2. 登録済みかつ、通常授業→講習への振替希望ではない →　競合あり登録不可
      if(isset($c) && count($c)==1) return false;
    }

  }

  public function get_time_slots($minute=30)
  {
    $ret = [];
    $_to_hour = $this->getToHourAttribute();
    $h = $this->getFromHourAttribute();
    for($i=$h;$i<intval($_to_hour);$i++){
      //nn_nn形式
      $c = 0;
      while($c < 60){
        $ret[sprintf('%02d', $i).sprintf('%02d', $c)] = true;
        $c+=$minute;
      }
    }
    return $ret;
  }
}
