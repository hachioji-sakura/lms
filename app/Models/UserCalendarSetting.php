<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\UserCalendar;
use App\Models\UserCalendarMemberSetting;
use DB;
use App\Models\Traits\Common;

class UserCalendarSetting extends UserCalendar
{
  use Common;
  protected $table = 'lms.user_calendar_settings';
  protected $guarded = array('id');
  public $register_mail_template = 'calendar_setting_new';
  public $delete_mail_template = 'calendar_setting_delete';
  public static $rules = array(
      'user_id' => 'required',
      'from_time_slot' => 'required',
      'to_time_slot' => 'required'
  );
  public function register_mail_title(){
    $title = __('messages.info_calendar_setting_add');
    return __('messages.mail_title_until_today').$title;

  }
  public function delete_mail_title(){
    return __('messages.info_calendar_setting_delete');
  }

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
      (
       (lms.user_calendar_settings.enable_end_date is null OR lms.user_calendar_settings.enable_end_date > ?)
      )
EOT;
    return $query->whereRaw($where_raw,[date('Y-m-d'),date('Y-m-d')]);
  }
  public function scopeSearchWord($query, $word)
  {
    $search_words = explode(' ', $word);
    $where_raw = <<<EOT
      user_calendar_settings.remark like ?
      OR user_calendar_settings.id in (
        select um.user_calendar_setting_id from user_calendar_member_settings um
        left join common.students s on s.user_id = um.user_id
        left join common.teachers t on t.user_id = um.user_id
        left join common.managers m on m.user_id = um.user_id
        where
          concat(s.name_last ,' ', s.name_first) like ?
          OR concat(t.name_last ,' ', t.name_first) like ?
          OR concat(m.name_last ,' ', m.name_first) like ?
          OR concat(s.kana_last ,' ', s.kana_first) like ?
          OR concat(t.kana_last ,' ', t.kana_first) like ?
          OR concat(m.kana_last ,' ', m.kana_first) like ?
       )
EOT;
    $query = $query->where(function($query)use($search_words, $where_raw){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhereRaw($where_raw,[$_like,$_like,$_like,$_like,$_like,$_like,$_like]);
        $query = $query->orWhere('id', $_search_word);
      }
    });
    return $query;
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
    if(app()->getLocale()=='en') return ucfirst($this->lesson_week);
    $ret =  $this->get_attribute_name('lesson_week', $this->lesson_week);
    return $ret.'曜';
  }
  public function schedule_method(){
    if(app()->getLocale()=='en'){
      return "(Every ".ucfirst($this->schedule_method).")";
    }
    return $this->get_attribute_name('schedule_method', $this->schedule_method);
  }
  public function week_setting(){
    /*
    $item = GeneralAttribute::get_item('schedule_method', $this->schedule_method);
    if(!isset($item)) return "";
    */
    $ret = "";
    if($this->lesson_week_count > 0){
      if(app()->getLocale()=='en') {
        $count_en = ['First', 'Second', 'Third', 'Forth', 'Fifth'];
        return $count_en[$this->lesson_week_count].' '.$this->lesson_week();
      }
      $ret .= '第'.$this->lesson_week_count;
    }
    $ret.=$this->lesson_week();
    return $ret;
  }
  public function timezone(){
    $base_date = '2000-01-01 ';
    $start_hour_minute = date('H:i',  strtotime($base_date.$this->from_time_slot));
    $end_hour_minute = date('H:i',  strtotime($base_date.$this->to_time_slot));
    return $start_hour_minute.'～'.$end_hour_minute;
  }
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.setting_status')[$this->status])){
      $status_name = config('attribute.setting_status')[$this->status];
    }
    return $status_name;
  }
  public function enable_name(){
    $enable_status = "disabled";
    if($this->status=='fix'){
      if($this->is_enable()==true) $enable_status = "enabled";
    }
    if(app()->getLocale()=='en') return $enable_status;
    if(isset(config('attribute.setting_status')[$enable_status])){
      $enable_status = config('attribute.setting_status')[$enable_status];
    }
    return $enable_status;
  }
  public function enable_date(){
    $start_date = '';
    $end_date = '';
    if(!empty($this->enable_start_date) && $this->enable_start_date != '9999-12-31') $start_date = date('Y/m/d', strtotime($this->enable_start_date));
    if(!empty($this->enable_end_date) && $this->enable_end_date != '9999-12-31') $end_date = date('Y/m/d', strtotime($this->enable_end_date));
    if(empty($start_date) && empty($end_date)) return '-';
    return $start_date.'～'.$end_date;
  }

  public function details($user_id=0){
    $item = $this;
    $base_date = '2000-01-01 ';

    $item['start_hours'] = date('H',  strtotime($base_date.$this->from_time_slot));
    $item['start_minutes'] = date('i',  strtotime($base_date.$this->from_time_slot));
    $item['end_hours'] = date('H',  strtotime($base_date.$this->to_time_slot));
    $item['end_minutes'] = date('i',  strtotime($base_date.$this->to_time_slot));
    $item['timezone'] = $this->timezone();
    $item['lesson'] = $this->lesson();
    $item['course'] = $this->course();
    $item['subject'] = $this->subject();
    $item['course_minutes_name'] = $this->course_minutes();
    $item['week_setting'] = $this->week_setting();
    $item['schedule_start_date'] = $this->dateweek_format($this->enable_start_date);
    $item['enable_date'] = $this->enable_date();
    $item['place_floor_name'] = "";
    $item['calendar_count'] = count($this->calendars);
    if(isset($this->place_floor)){
      $item['place_floor_name'] = $this->place_floor->name();
    }
    $item['work_name'] = $this->work();
    $item['subject'] = $this->subject();
    $item['status_name'] = $this->status_name();
    $item['course_name'] = $this->lesson().'/'.$this->course().'/'.$item['course_minutes_name'];
    $item['repeat_setting_name'] = $this->schedule_method().$this->week_setting().'/'.$item['timezone'];
    $item['last_schedule'] = $this->get_add_last_schedule();

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
    if($this->work != 9){
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
    }
    else {
      $item['title'] = $item['user_name'].'/'.$item['work_name'];
    }
    return $item;
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){

    $ret = [];
    $trial_id = 0;
    //TODO 重複登録、競合登録の防止が必要
    /*
    $user = User::where('id', $form['user_id'])->first();
    if(!isset($user)) return null;

    foreach($user->calendar_settings as $setting){
      if($setting->is_conflict_setting($form["schedule_method"], $form["lesson_week_count"], $form['lesson_week'], $form['from_time_slot'], $form['to_time_slot'], 0, $form['place_floor_id'])==true){
        \Log::error("user_calendar_settings[id=".$setting->id."]:と競合");
        return null;
      }
    }
    */
    if(isset($form['trial_id'])) $trial_id = $form['trial_id'];

    $course_minutes = intval(strtotime('2000-01-01 '.$form['to_time_slot']) - strtotime('2000-01-01 '.$form['from_time_slot']))/60;
    $status = 'new';
    if(isset($form['work']) && $form['work']==9) $status = 'fix';
    $target_user = null;
    if(isset($form['target_user_id']) && $form['target_user_id']>0) $target_user = User::where('id', $form['target_user_id'])->first();
    if(isset($target_user)){
      //休会の場合、生成されるケースがある場合は、キャンセル扱いで入れる
      $target_user = $target_user->details();
      if($target_user->status=='recess'){
        $status = 'cancel';
      }
      if($target_user->status=='unsubscribe'){
        $controller = new Controller;
        return $controller->error_response("unsubscribe", "この予定主催者は退職（退会）しています");
      }
    }

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
      'course_minutes' => $course_minutes,
      'work' => $form['work'],
      'remark' => $form['remark'],
      'from_time_slot' => $form['from_time_slot'],
      'to_time_slot' => $form['to_time_slot'],
      'create_user_id' => $form['create_user_id'],
      'status' => $status
    ]);
    $calendar_setting->memberAdd($form['user_id'], $form['create_user_id'], '主催者', false);
    $calendar_setting->change($form);

    return $calendar_setting->api_response(200, "", "", $calendar_setting);
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose($login_user_id, $is_send_mail=true){
    if($this->status!='new' && $is_send_mail==true){
      $this->delete_mail([], $login_user_id);
    }

    //事務システム側を先に削除
    $this->office_system_api("DELETE");
    UserCalendarTagsetting::where('user_calendar_setting_id', $this->id)->delete();
    UserCalendarMemberSetting::where('user_calendar_setting_id', $this->id)->delete();
    /*
    $calendars = UserCalendar::where('user_calendar_setting_id', $this->id)->get();
    foreach($calendars as $calendar){
      $calendar->dispose();
    }
    */
    $this->delete();
  }
  public function change($form){
    \Log::warning("UserCalendarSetting::change");
    $update_fields = [
      'from_time_slot', 'to_time_slot', 'lesson_week', 'lesson_week_count', 'schedule_method', 'place_floor_id',
      'remark', 'place', 'work', 'enable_start_date', 'enable_end_date', 'lecture_id', 'status',
    ];

    if(isset($form['course_type']) && !isset($form['work'])){
      //TODO Workの補間どうにかしたい
      \Log::warning("course_type=:".$form['course_type']);
      $work_data = ["single" => 6, "group"=>7, "family"=>8];
      $form['work'] = $work_data[$form["course_type"]];
    }

    $lecture_id = 0;
    \Log::warning("TODO lectureの補間どうにかしたい");
    if(isset($form['lesson']) && isset($form['course_type'])){
      $course_id = config('replace.course')[$form["course_type"]];
      $lecture = Lecture::where('lesson', $form['lesson'])
          ->where('course', $course_id)
          ->first();
      $lecture_id = $lecture->id;
    }
    $form['lecture_id'] = $lecture_id;
    $data = [];

    if(array_key_exists('enable_end_date', $form)){
      $data['enable_end_date'] = null;
    }

    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      $data[$field] = $form[$field];
    }
    if(isset($data['from_time_slot']) && isset($data['to_time_slot'])){
      //course_minutesは、time_slotから補完
      $data['course_minutes'] = intval(strtotime('2000-01-01 '.$data['to_time_slot']) - strtotime('2000-01-01 '.$data['from_time_slot']))/60;

      $lesson_week = $this->lesson_week;
      if(isset($data['lesson_week'])){
        $lesson_week = $data['lesson_week'];
      }
      $place_floor_id = $this->place_floor_id;
      if(isset($data['place_floor_id'])){
        $place_floor_id = $data['place_floor_id'];
      }
      //TODO 重複登録、競合登録の防止が必要
      /*
        $user = User::where('id', $this->user_id)->first();
      if(!isset($user)) return null;
      foreach($user->calendar_settings as $setting){
        if($setting->id == $this->id) continue;
        if($setting->is_conflict_setting($this->schedule_method, $this->lesson_week_count, $lesson_week, $data['from_time_slot'], $data['to_time_slot'], 0, $place_floor_id)==true){
          \Log::error("user_calendar_settings[id=".$setting->id."]:と競合");
          return null;
        }
      }
      */
    }
    UserCalendarSetting::where('id', $this->id)->update($data);
    $tag_names = ['course_type', 'lesson', 'is_online'];
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

    if(isset($member)){
      $member->update(['remark' => $remark]);
    }
    else {
      $member = UserCalendarMemberSetting::create([
          'user_calendar_setting_id' => $this->id,
          'user_id' => $user_id,
          'remark' => $remark,
          'create_user_id' => $create_user_id,
      ]);
      $member->set_api_lesson_fee();
      if($is_api===true){
        //事務システムにも登録
        $member->office_system_api("POST");
      }
    }
    return $member;
  }
  public function get_add_last_schedule(){
    $ret = $this->calendars->sortByDesc('start_time')->first();
    if(isset($ret)){
      $ret = $ret->details(1);
    }
    return $ret;
  }
  public function get_add_calendar_date($start_date="", $end_date="", $range_month=1, $month_week_count=5){
    $add_calendar_date = [];
    if(empty($start_date)) $start_date = date('Y-m-01'); //月初

    if(!empty($this->enable_start_date)){
      if(strtotime($start_date) < strtotime($this->enable_start_date)){
        //月初以降の有効開始日の場合、そちらを使う
        $start_date = $this->enable_start_date;
      }
    }

    //終了日指定がない場合、range_monthを使う
    if(empty($end_date)) $end_date = date('Y-m-t', strtotime(date('Y-m-01') . '+'.$range_month.' month'));

    if(!empty($this->enable_end_date)){
      if(strtotime($end_date) > strtotime($this->enable_end_date)){
        //1か月後月末以前に設定が切れる場合、そちらを使う
        $end_date = $this->enable_end_date;
      }
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
      //休校日ではない場合、候補とする
      if(!$this->is_holiday($target_date,false,true)){
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
      }
      //次の登録日 ７日後
      $target_date = date("Y-m-d", strtotime("+7 day ".$target_date));
      \Log::warning("target_date=".$target_date);
      if(date("m", strtotime($target_date)) > $target_month){
        //月が変わった
        $c = $month_week_count;
        $target_month = date("m", strtotime($target_date));
      }
    } while(strtotime($end_date) >= strtotime($target_date));

    $ret = [];
    foreach($add_calendar_date as $date){
      $ret[$date] = ['already_calendars'=>[], 'setting' => $this->details(1)];
      \Log::warning("date=".$date);
      $_calendars = $this->calendars->where('start_time', $date.' '.$this->from_time_slot)
        ->where('end_time', $date.' '.$this->to_time_slot);

      $is_member = true;
      if(count($_calendars) > 0){
        //同じ予定がすでに作成済み
        $ret[$date]['already_calendars'] = $_calendars;
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
  public function is_enable($date=""){
    $strtotime = -1;
    if(empty($date)) $strtotime = strtotime('now');
    else $strtotime = strtotime($date);

    if(!empty($this->enable_start_date)){
      $diff = strtotime($this->enable_start_date) - $strtotime;
      if($diff > 0){
        //設定開始前
        return false;
      }
    }
    if(!empty($this->enable_end_date)){
      $diff = strtotime($this->enable_end_date) - $strtotime;
      if($diff < 0){
        //設定終了
        return false;
      }
    }
    return true;
  }
  public function has_enable_member(){
    $is_enable = true;
    if($this->work!=9){
      $is_enable = false;
      foreach($this->members as $member){
        if($this->user_id == $member->user_id) continue;
        if($member->user->details()->status != 'regular' && $member->user->details()->status != 'trial') continue;
        $is_enable = true;

        break;
      }
    }
    return $is_enable;
  }
  /**
   * 引数の値で登録時に競合する場合 trueを返す
   */
  public function is_conflict_setting($schedule_method, $week_count, $week, $start_time, $end_time, $place_id=0, $place_floor_id=0){
    //設定が有効じゃない＝競合は発生しない
    if($this->is_enable() === false) return false;
    if($this->lesson_week != $week) return false;
    if($schedule_method!="week"){
      //月の場合、何週目かチェック
      if($this->week_count != $week_count) return false;
    }
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

  public function setting_to_calendar($start_date="", $end_date="", $range_month=1, $month_week_count=5){
    $data = [];
    \Log::warning("setting_to_calendar:[".$start_date."][".$end_date."][".$range_month."][".$month_week_count."]");

    $schedules = $this->get_add_calendar_date($start_date, $end_date, $range_month, $month_week_count);
    foreach($schedules as $date => $already_calendar){
      if(isset($already_calendar['already_calendars']) && count($already_calendar['already_calendars'])>0){
        //作成済みの場合
        continue;
      }
      $data[] = $this->add_calendar($date);
    }
    \Log::warning("setting_to_calendar:res=[".count($data)."]");
    return $data;
  }

  //この設定を使って、引数＝日付でUserCalendarに登録する
  public function add_calendar($date){
    $is_enable = true;
    /*
    TODO:体験の場合、未来の開始日でも予定を登録することがある
    $is_enable = $this->is_enable();
    if($is_enable==false){
      return $this->error_response("valid_setting", "設定が有効ではない(id=".$this->id.")");
    }
    */
    if($this->work!=9){
      $is_enable = $this->has_enable_member();
      if($is_enable==false){
        \Log::error("有効なメンバーがいない");
        return $this->error_response("no_member", "有効なメンバーがいない(id=".$this->id.")");
      }
    }

    //担当講師が本登録でない場合、登録できない
    //if($this->user->status!='regular') return null;

    $start_time = $date.' '.$this->from_time_slot;
    $end_time = $date.' '.$this->to_time_slot;

    //通常授業設定と競合するカレンダーが存在するかチェック
    $c = (new UserCalendar())->rangeDate($start_time, $end_time)
        ->where('user_calendar_setting_id', $this->id)
        ->where('user_id', $this->user_id)
        ->first();

    if(isset($c)){
      return $this->error_response("already_registered", "このカレンダーは登録ずみ(id=".$this->id.")");
    }

    $c = (new UserCalendar())->rangeDate($start_time, $end_time)
        ->where('user_calendar_setting_id','!=', $this->id)
        ->where('user_id', $this->user_id)
        ->first();

    $default_status = 'fix';
    if(isset($c)){
      \Log::warning("calendar:[".$c->id."]");
      //通常授業設定と競合するカレンダーが存在
      $default_status = 'new';
    }

    $form = [
      'user_calendar_setting_id' => $this->id,
      'start_time' => $start_time,
      'end_time' => $end_time,
      'lecture_id' => $this->lecture_id,
      'place_floor_id' => $this->place_floor_id,
      'work' => $this->work,
      'exchanged_calendar_id' => 0,
      'remark' => $this->remark,
      'target_user_id' => $this->user_id,
      'create_user_id' => 1,
    ];

    foreach($this->tags as $tag){
      $form[$tag->tag_key] = $tag->tag_value;
    }

/*
    foreach($form as $key => $v){
      \Log::warning("param[".$key."] =".$v);
    }
*/
    $res = UserCalendar::add($form);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $calendar = $res['data'];

    foreach($this->members as $member){
      if($this->user_id == $member->user_id) continue;
      if(strtotime($member->user->created_at) > strtotime($date)) continue;
      if($member->user->details()->status != 'regular' && $member->user->details()->status != 'trial') continue;
      //主催者以外を追加
      $calendar->memberAdd($member->user_id, 1, $default_status);
    }
    if($default_status=='fix'){
      UserCalendarMember::where('calendar_id', $calendar->id)->update(['status' => $default_status]);
      $calendar->update(['status' => $default_status]);
    }
    return $this->api_response(200, "", "", $calendar);
  }
  public function set_status(){
    parent::set_status();
    if($this->status=='fix'){
      $start_date = $this->enable_start_date;
      $end_date = $this->enable_end_date;
      if(strtotime($start_date) < strtotime(date('Y-m-1'))){
        //今月1日より以前なら、今月1日を登録開始にする
        $start_date = date('Y-m-1');
      }
      if(empty($end_date)){
        //設定がないなら来月末
        $end_date = date('Y-m-t', strtotime('+1 month'));
      }
      \Log::warning("mogumogu(".$start_date.":".$end_date.")");
      $this->setting_to_calendar($start_date, $end_date, 1, 5);
    }
  }

}
