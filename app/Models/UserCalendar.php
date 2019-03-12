<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentParent;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\Lecture;

class UserCalendar extends Model
{
  protected $pagenation_line = 20;
  protected $table = 'user_calendars';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'start_time' => 'required',
      'end_time' => 'required'
  );
  public function scopeSortStarttime($query, $sort){
    if(empty($sort)) $sort = 'asc';
    return $query->orderBy('start_time', $sort);
  }
  public function scopePagenation($query, $page, $line){
    $_line = $this->pagenation_line;
    if(is_numeric($line)){
      $_line = $line;
    }
    $_page = 0;
    if(is_numeric($page)){
      $_page = $page;
    }
    $_offset = $_page*$_line;
    if($_offset < 0) $_offset = 0;
    return $query->offset($_offset)->limit($_line);
  }
  public function scopeRangeDate($query, $from_date, $to_date=null)
  {
    $field = 'start_time';
    //日付検索
    if(!empty($from_date)){
      $query = $query->where($field, '>=', $from_date);
    }
    if(!empty($to_date)){
      $query = $query->where($field, '<', $to_date);
    }
    return $query;
  }
  public function scopeSearchDate($query, $from_date, $to_date)
  {
    $where_raw = <<<EOT
      ((user_calendars.start_time >= '$from_date'
       AND user_calendars.start_time < '$to_date'
      )
      OR (user_calendars.end_time >= '$from_date'
        AND user_calendars.end_time < '$to_date'
      ))
EOT;
    return $query->whereRaw($where_raw,[$from_date, $to_date]);

  }
  public function scopeFindStatuses($query, $statuses, $is_not=false)
  {
    if($is_not===true){
      $query = $query->whereNotIn('status', $statuses);
    }
    else {
      $query = $query->whereIn('status', $statuses);
    }
    return $query;
  }

  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      user_calendars.id in (select calendar_id from user_calendar_members where user_id=$user_id)
EOT;

    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function is_access($user_id){
    $parent = StudentParent::where('user_id', $user_id)->first();
    $ret = "";
    if(isset($parent)){
      //保護者の場合
      foreach ($parent->relation() as $relation){
        if($this->_is_access($relation->student->user_id)==true){
          return $ret;
        }
      }
      return $ret;
    }
    else {
      return $this->_is_access($user_id);
    }
  }
  public function _is_access($user_id){
    foreach($this->members as $member){
      if($user_id==$member->user_id){
        return true;
      }
    }
    return false;
  }
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
  public function status_style(){
    $status_name = "";
    switch($this->status){
      case "confirm":
        return "warning";
      case "fix":
        return "primary";
      case "rest":
      case "absence":
        return "danger";
      case "presence":
        return "success";
    }
    return "secondary";
  }
  public function status_name(){
    $status_name = "";
    switch($this->status){
      case "new":
        return "予定(下書き)";
      case "confirm":
        return "予定確認中";
      case "fix":
        return "授業予定";
      case "cancel":
        return "キャンセル";
      case "rest":
        return "休み";
      case "absence":
        return "欠席";
      case "presence":
        return "出席済み";
    }
    return "";
  }
  public function subject(){
    if($this->trial_id > 0) return "体験授業";
    if(!isset($this->lecture)) return "";
    $lecture = $this->lecture->details();
    return $lecture['subject']->attribute_name;
  }
  public function lesson(){
    if(!isset($this->lecture)) return "";
    $lecture = $this->lecture->details();
    return $lecture['lesson']->attribute_name;
  }
  public function course(){
    if(!isset($this->lecture)) return "";
    $lecture = $this->lecture->details();
    return $lecture['course']->attribute_name;
  }
  public function timezone(){
    $start_hour_minute = date('H:i',  strtotime($this->start_time));
    $end_hour_minute = date('H:i',  strtotime($this->end_time));
    return $start_hour_minute.'～'.$end_hour_minute;
  }

  public function details(){
    $item = $this;
    $item['status_name'] = $this->status_name();
    $item['place'] = $this->place();
    $item['date'] = date('Y/m/d',  strtotime($this->start_time));
    $item['start_hour_minute'] = date('H:i',  strtotime($this->start_time));
    $item['end_hour_minute'] = date('H:i',  strtotime($this->end_time));
    $item['timezone'] = $this->timezone();
    $item['datetime'] = date('m月d日 H:i',  strtotime($this->start_time)).'～'.$item['end_hour_minute'];
    if($this->lecture_id > 0){
      $lecture = $this->lecture->details();
    }
    $item['lesson'] = $this->lesson();
    $item['course'] = $this->course();
    $item['subject'] = $this->subject();
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

    $item['is_exchange'] = false;
    if(is_numeric($item['exchanged_calendar_id']) && $item['exchanged_calendar_id']>0){
      $item['is_exchange'] = true;
    }
    return $item;
  }
  static protected function add($form){
    $ret = [];
    $trial_id = 0;
    if(isset($form['trial_id'])){
      $trial_id = $form['trial_id'];
    }
    $calendar = UserCalendar::create([
      'start_time' => $form['start_time'],
      'end_time' => $form['end_time'],
      'lecture_id' => 0,
      'trial_id' => $trial_id,
      'place' => '',
      'work' => '',
      'remark' => '',
      'user_id' => $form['teacher_user_id'],
      'create_user_id' => $form['create_user_id'],
      'status' => 'new'
    ]);
    $calendar->memberAdd($form['teacher_user_id'], $form['create_user_id']);
    $calendar->change($form);
    return $calendar;
  }
  protected function change($form){
    $lecture_id = 0;
    if(isset($form['lesson']) && isset($form['subject']) && isset($form['course'])){
      $lecture = Lecture::where('lesson' , $form['lesson'])
        ->where('course' , $form['course'])
        ->where('subject' , $form['subject'])->first();
        if(isset($lecture)){
          $lecture_id = $lecture->id;
        }
        else {
          //レクチャがなければ追加
          $lecture = Lecture::create([
            'lesson' => $form['lesson'],
            'course' => $form['course'],
            'subject' => $form['subject'],
          ]);
          $lecture_id = $lecture->id;
        }
    }
    $update_fields = [
      'start_time', 'end_time', 'remark', 'place', 'work'
    ];
    $data = [
      'lecture_id' => $lecture_id,
    ];
    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      $data[$field] = $form[$field];
    }
    $this->update($data);
    return $this;
  }
  public function memberAdd($user_id, $create_user_id, $status='new'){
    if(empty($user_id) || $user_id < 1) return null;
    $member = UserCalendarMember::create([
        'calendar_id' => $this->id,
        'user_id' => $user_id,
        'status' => $status,
        'create_user_id' => $create_user_id,
    ]);
    return $member;
  }
  public function is_member($user_id){
    foreach($this->members as $member){
      if($member->user_id === $user_id){
        return true;
      }
    }
    return false;
  }
  public function is_conflict($start, $end){
    $start = strtotime($start);
    $end = strtotime($end);
    $calendar_starttime = strtotime($this->start_time);
    $calendar_endtime = strtotime($this->end_time);
    if($start > $calendar_starttime && $start < $calendar_endtime){
        return true;
    }
    if($end > $calendar_starttime && $end < $calendar_endtime){
      return true;
    }
    if($start==$calendar_starttime && $end == $calendar_endtime){
      return true;
    }
    return false;
  }
}
