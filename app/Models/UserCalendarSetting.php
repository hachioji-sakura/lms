<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\UserCalendar;
use App\Models\UserCalendarMemberSetting;
use DB;
class UserCalendarSetting extends UserCalendar
{
  protected $table = 'lms.user_calendar_settings';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'from_time_slot' => 'required',
      'to_time_slot' => 'required'
  );
  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      lms.user_calendar_settings.id in (select user_calendar_setting_id from lms.user_calendar_member_settings where user_id=$user_id)
EOT;

    return $query->whereRaw($where_raw,[]);
  }
  public function scopeFindTrialStudent($query, $trial_id)
  {
    $where_raw = <<<EOT
      lms.user_calendar_settings.id in (
        select user_calendar_setting_id from
        lms.user_calendar_member_settings ums
        inner join common.students s on s.user_id = ums.user_id
        inner join lms.trial_students ts on s.id = ts.student_id
        where ts.trial_id=?)
EOT;

    return $query->whereRaw($where_raw,[$trial_id]);
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMemberSetting', 'user_calendar_setting_id');
  }
  public function calendars(){
    return $this->hasMany('App\Models\UserCalendar', 'user_calendar_setting_id');
  }
  public function tags(){
    return $this->hasMany('App\Models\UserCalendarTagSetting', 'user_calendar_setting_id');
  }
  public function scopeFindWeeks($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'lesson_week', $vals, $is_not);
  }
  public function scopeEnable($query){
    $where_raw = <<<EOT
      lms.user_calendar_settings.status = 'fix'
      AND (
       (lms.user_calendar_settings.enable_start_date is null OR lms.user_calendar_settings.enable_start_date < ?)
       AND
       (lms.user_calendar_settings.enable_end_date is null OR lms.user_calendar_settings.enable_end_date > ?)
      )
EOT;
    return $query->whereRaw($where_raw,[date('Y-m-d'),date('Y-m-d')]);
  }
  public function scopeOrderByWeek($query){
    $weeks = [];
    foreach(config('attribute.lesson_week') as $index=>$name){
      $weeks[] = "'".$index."'";
    }
    $weeks_order = implode(',', $weeks);
    $query = $query->orderByRaw(DB::raw("FIELD(lesson_week, $weeks_order)"));
    return $query->orderBy('from_time_slot', 'asc');
  }
  public function lesson_week(){
    return $this->get_attribute_name('lesson_week', $this->lesson_week);
  }
  public function schedule_method(){
    return $this->get_attribute_name('schedule_method', $this->schedule_method);
  }
  public function week_setting(){
    $item = GeneralAttribute::where('attribute_key', 'schedule_method')
      ->where('attribute_value', $this->schedule_method)
      ->first();
    if(!isset($item)) return "";
    $ret = "";
    if($this->lesson_week_count > 0){
      $ret .= '第'.$this->lesson_week_count;
    }
    $ret.=$this->lesson_week().'曜';
    return $ret;
  }
  public function timezone(){
    $base_date = '2000-01-01 ';
    $start_hour_minute = date('H:i',  strtotime($base_date.$this->from_time_slot));
    $end_hour_minute = date('H:i',  strtotime($base_date.$this->to_time_slot));
    return $start_hour_minute.'～'.$end_hour_minute;
  }
  public function details($user_id=0){
    $item = $this;
    $base_date = '2000-01-01 ';
    $item['start_hours'] = date('H',  strtotime($base_date.$this->from_time_slot));
    $item['start_minutes'] = date('i',  strtotime($base_date.$this->from_time_slot));
    $item['timezone'] = $this->timezone();
    $item['lesson'] = $this->lesson();
    $item['course'] = $this->course();
    $item['subject'] = $this->subject();
    $item['course_minutes_name'] = $this->course_minutes();
    $item['week_setting'] = $this->week_setting();
    $item['place_floor_name'] = "";
    if(isset($this->place_floor)){
      $item['place_floor_name'] = $this->place_floor->name();
    }
    $item['work_name'] = $this->work();
    $item['subject'] = $this->subject();
    $item['status_name'] = $this->status_name();

    $teacher_name = "";
    $student_name = "";
    $other_name = "";
    $teachers = [];
    $students = [];
    $managers = [];
    foreach($this->members as $member){
      $_member = $member->user->details('teachers');
      if($_member->role === 'teacher'){
        $teacher_name.=$_member['name'].',';
        $teachers[] = $member;
      }
      $_member = $member->user->details('managers');
      if($_member->role === 'manager'){
        $other_name.=$_member['name'].',';
        $managers[] = $member;
      }
      $_member = $member->user->details('students');
      if($_member->role === 'student'){
        $student_name.=$_member['name'].',';
        $students[] = $member;
      }
    }
    unset($item['members']);
    unset($item['lecture']);
    $item['teachers'] = $teachers;
    $item['students'] = $students;
    $item['managers'] = $managers;
    $item['student_name'] = trim($student_name,',');
    $item['teacher_name'] = trim($teacher_name,',');
    $item['manager_name'] = trim($other_name,',');
    $item['user_name'] = $this->user->details()->name();

    $item['title'] = $item['teacher_name'].'/'.$item['lesson'].'/'.$item['course'].'/'. $item["course_minutes_name"].'/';
    foreach($this->subject() as $subject){
      $item['title'].=$subject.'/';
    }

    $item['title'] = trim($item['title'], '/');
    $item['title2']  = "";
    if($item->is_teaching()===true){
      //授業について詳細を表示
      $item['title2'] = $item['lesson'].' / '.$item['course'].' / 授業時間：'.$item['course_minutes_name'];
    }

    return $item;
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){
    $ret = [];
    $trial_id = 0;

    $user = User::where('id', $form['user_id'])->first();
    if(!isset($user)) return null;

    foreach($user->calendar_settings as $setting){
      if($setting->is_conflict_setting($form['lesson_week'], $form['from_time_slot'], $form['to_time_slot'], 0, $form['place_floor_id'])==true){
        \Log::error("user_calendar_settings[id=".$setting->id."]:と競合");
        return null;
      }
    }
    if(isset($form['trial_id'])) $trial_id = $form['trial_id'];

    //TODO Workの補間どうにかしたい
    if(isset($form['course_type']) && !isset($form['work'])){
      $work_data = ["single" => 6, "group"=>7, "family"=>8];
      $form['work'] = $work_data[$form["course_type"]];
    }

    //TODO:日にち指定、月末日指定の場合は別メソッドで対応する
    $calendar_setting = UserCalendarSetting::create([
      'user_id' => $form['user_id'],
      'trial_id' => $trial_id,
      'schedule_method' => $form['schedule_method'],
      'lesson_week_count' => $form['lesson_week_count'],
      'lesson_week' => $form['lesson_week'],
      'place_floor_id' => $form['place_floor_id'],
      'work' => $form['work'],
      'remark' => $form['remark'],
      'from_time_slot' => $form['from_time_slot'],
      'to_time_slot' => $form['to_time_slot'],
      'create_user_id' => $form['create_user_id'],
    ]);
    $calendar_setting->memberAdd($form['user_id'], $form['create_user_id'], '主催者', false);
    $calendar_setting->change($form);
    return $calendar_setting;
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    //事務システム側を先に削除
    $this->office_system_api("DELETE");
    UserCalendarTagsetting::where('user_calendar_setting_id', $this->id)->delete();
    UserCalendarMemberSetting::where('user_calendar_setting_id', $this->id)->delete();
    $this->delete();
  }
  public function change($form){
    $update_fields = [
      'from_time_slot', 'to_time_slot', 'lesson_week', 'lesson_week_count', 'schedule_method',
      'remark', 'place', 'work', 'enable_start_date', 'enable_end_date', 'lecture_id', 'status',
    ];
    $form['lesson_week'] = $this->lesson_week;
    $form['from_time_slot'] = $this->from_time_slot;
    $form['to_time_slot'] = $this->to_time_slot;

    if(isset($form['course_minutes']) && isset($form['start_hours']) && isset($form['start_minutes'])){
      //画面のフォーム（開始時間、授業時間）　→　開始時間、終了時間更新
      $form['from_time_slot'] = $form['start_hours'].':'.$form['start_minutes'].':00';
      $minutes = $form['course_minutes'];
      $form['to_time_slot'] = date("H:i:00", strtotime('+'.$minutes.' minute '.'2000-01-01 '.$form['from_time_slot']));
    }

    if(isset($form['course_type']) && !isset($form['work'])){
      //TODO Workの補間どうにかしたい
      \Log::warning("TODO Workの補間どうにかしたい course_type=:".$form['course_type']);
      $work_data = ["single" => 6, "group"=>7, "family"=>8];
      $form['work'] = $work_data[$form["course_type"]];
      \Log::warning("TODO Workの補間どうにかしたい work=:".$form['work']);
    }

    //TODO lectureの補間どうにかしたい
    $lecture_id = 0;
    if(isset($form['lesson']) && isset($form['course_type'])){
      $course_id = config('replace.course')[$form["course_type"]];
      $lecture = Lecture::where('lesson', $form['lesson'])
          ->where('course', $course_id)
          ->first();
      $lecture_id = $lecture->id;
    }
    $form['lecture_id'] = $lecture_id;
    $data = [];
    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      $data[$field] = $form[$field];
    }

    if(isset($data['from_time_slot']) && isset($data['to_time_slot'])){
      $lesson_week = $this->lesson_week;
      if(isset($data['lesson_week'])){
        $lesson_week = $data['lesson_week'];
      }
      $place_floor_id = $this->place_floor_id;
      if(isset($data['place_floor_id'])){
        $place_floor_id = $data['place_floor_id'];
      }
      $user = User::where('id', $this->user_id)->first();
      if(!isset($user)) return null;
      foreach($user->calendar_settings as $setting){
        if($setting->id == $this->id) continue;
        if($setting->is_conflict_setting($lesson_week, $data['from_time_slot'], $data['to_time_slot'], 0, $place_floor_id)==true){
          \Log::error("user_calendar_settings[id=".$setting->id."]:と競合");
          return null;
        }
      }
    }

    $this->update($data);
    $tag_names = ['course_minutes', 'course_type', 'lesson'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTagSetting::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    $tag_names = ['charge_subject', 'kids_lesson', 'english_talk_lesson', 'piano_lesson'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTagSetting::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    //事務システムも更新
    $this->office_system_api("PUT");
    return $this;
  }
  public function memberAdd($user_id, $create_user_id, $remark='', $is_api=true){
    if(empty($user_id) || $user_id < 1) return null;

    $member = UserCalendarMemberSetting::where('user_calendar_setting_id' , $this->id)
      ->where('user_id', $user_id)->first();

    if(isset($memeber)){
      $member = $memeber->update(['remark', $remark]);
    }
    else {
      $member = UserCalendarMemberSetting::create([
          'user_calendar_setting_id' => $this->id,
          'user_id' => $user_id,
          'remark' => $remark,
          'create_user_id' => $create_user_id,
      ]);
      if($is_api===true){
        //事務システムにも登録
        $member->office_system_api("POST");
      }
    }
    return $member;
  }
  public function get_add_calendar_date($start_date="", $range_month=1, $month_week_count=5){
    $add_calendar_date = [];
    if(empty($start_date)) $start_date = date('Y-m-01'); //月初

    if(isset($this->enable_start_date)){
      if(strtotime($start_date) < strtotime($this->enable_start_date)){
        //月初以降の有効開始日の場合、そちらを使う
        $start_date = $this->enable_start_date;
      }
    }
    //1か月後の月末
    $end_date = date('Y-m-t', strtotime(date('Y-m-01') . '+'.$range_month.' month'));

    if(strtotime($end_date) < strtotime($this->enable_end_date)){
      //1か月後月末以前に設定が切れる場合、そちらを使う
      $end_date = $this->enable_end_date;
    }

    //echo $start_date."\n";
    $_w = date('w', strtotime($start_date));
    $_week_no = [
        "sun" => 0 , "mon" => 1, "tue" => 2, "wed" => 3, "thi" => 4 , "fri" => 5, "sat" =>6
    ];
    $w = $_week_no[$this->lesson_week];
    $dw = $w - $_w;
    if($dw < 0) $dw += 7;
    //最初の登録日＝start_dateから近い対象の曜日
    $target_date = date("Y-m-d", strtotime("+".$dw." day ".$start_date));
    if($this->lesson_week_count > 0) $month_week_count=1;
    $c = $month_week_count;
    $target_month = date("m", strtotime($target_date));

    do {
      $is_add = true;
      if($this->lesson_week_count > 0){
        $is_add = false;
        //第何週か指定がある場合
        $c = $this->get_week_count($target_date);
        if($c == $this->lesson_week_count){
          //echo $c.' '.$target_date."\n";
          $is_add = true;
        }
      }
      if($is_add===true && $c > 0){
        $add_calendar_date[] = $target_date;
        $c--;
      }
      //次の登録日 ７日後
      $target_date = date("Y-m-d", strtotime("+7 day ".$target_date));
      if(date("m", strtotime($target_date)) > $target_month){
        //月が変わった
        $c = $month_week_count;
        $target_month = date("m", strtotime($target_date));
      }
    } while(strtotime($end_date) > strtotime($target_date));

    $ret = [];
    foreach($add_calendar_date as $date){
      $ret[$date] = [];
      $_calendars = $this->calendars->where('start_time', $date.' '.$this->from_time_slot)
        ->where('end_time', $date.' '.$this->to_time_slot);

      $is_member = true;
      if(count($_calendars) > 0){
        //同じ日付のカレンダーがすでに作成済み
        $ret[$date] = $_calendars;
      }
    }
    return $ret;
  }
  /* https://generation1986.g.hatena.ne.jp/primunu/20080317/1205767155
  */
  private function get_week_count($target_date){
      $saturday = 6;
      $week_day = 7;
      $w = intval(date('w', strtotime($target_date)));
      $d = intval(date('d', strtotime($target_date)));
      $w = ($saturday - $w) + $d;
      return ceil($w/$week_day);
  }

  /**
   * 引数の値で登録時に競合する場合 trueを返す
   */
  public function is_conflict_setting($week, $start_time, $end_time, $place_id=0, $place_floor_id=0){
    if($this->lesson_week != $week) return false;

    $start = strtotime('2000-01-01 '.$start_time);
    $end = strtotime('2000-01-01 '.$end_time);
    $calendar_starttime = strtotime('2000-01-01 '.$this->from_time_slot);
    $calendar_endtime = strtotime('2000-01-01 '.$this->to_time_slot);

    if($start > $calendar_starttime && $start < $calendar_endtime){
      //開始時刻が、範囲内
      return true;
    }
    if($end > $calendar_starttime && $end < $calendar_endtime){
      //終了時刻が、範囲内
      return true;
    }
    if($start <= $calendar_starttime && $end >= $calendar_endtime){
      //内包しているケース
      return true;
    }
    if($calendar_starttime <= $start && $calendar_endtime >= $end){
      //内包しているケース
      return true;
    }

    if($end == $calendar_starttime || $start == $calendar_endtime){
      //開始終了が一致した場合、場所をチェック
      if(!empty($place_id) && $this->is_same_place($place_id)===false){
        //場所が異なるのでスケジュール競合
        return true;
      }
      else if(!empty($place_floor_id) && $this->is_same_place(0,$place_floor_id)===false){
        //場所が異なるのでスケジュール競合
        return true;
      }
    }
    return false;
  }
}
