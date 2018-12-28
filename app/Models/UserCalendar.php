<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserCalendar;

class UserCalendar extends Model
{
  protected $table = 'user_calendars';
  protected $guarded = array('id');
  public static $rules = array(
      'lecture_id' => 'required',
      'start_time' => 'required',
      'end_time' => 'required'
  );
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMember', 'calendar_id');
  }
  public function place(){
    $item = GeneralAttribute::place($this->place)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  public function status_name(){
    $status_name = "";
    switch($this->status){
      case "new":
        return "仮登録";
      case "cancel":
        return "キャンセル";
      case "fix":
        return "確定";
    }
    return "";
  }
  public function details(){
    $item = $this;
    $item['status_name'] = $this->status_name();
    $item['place'] = $this->place();
    $item['date'] = date('Y/m/d',  strtotime($this->start_time));
    $item['start'] = date('H:i',  strtotime($this->start_time));
    $item['end'] = date('H:i',  strtotime($this->end_time));

    $lecture = $this->lecture->details();
    $item['subject'] = $lecture['subject']->attribute_name;
    $item['lesson'] = $lecture['lesson']->attribute_name;
    $item['course'] = $lecture['course']->attribute_name;

    $teacher_name = "";
    $student_name = "";
    $other_name = "";
    foreach($this->members as $member){
      $_member = $member->user->details();
      if($_member->role === 'student'){
        $student_name.=$_member['name'].',';
      }
      else if($_member->role === 'teacher'){
        $teacher_name.=$_member['name'].',';
      }
      else {
        $other_name.=$_member['name'].',';
      }
    }
    unset($item['members']);
    unset($item['lecture']);
    $item['student_name'] = trim($student_name,',');
    $item['teacher_name'] = trim($teacher_name,',');
    $item['other_name'] = trim($other_name,',');
    return $item;
  }
}
