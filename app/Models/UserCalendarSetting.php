<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserCalendar;
use App\Models\UserCalendarMemberSetting;

class UserCalendarSetting extends Model
{
  protected $table = 'user_calendar_settings';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'from_time_slot' => 'required',
      'to_time_slot' => 'required'
  );
  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      user_calendar_settings.id in (select user_calendar_setting_id from user_calendar_setting_members where user_id=$user_id)
EOT;

    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMemberSetting', 'user_calendar_setting_id');
  }
  public function place(){
    $item = GeneralAttribute::place($this->place)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  public function work(){
    $item = GeneralAttribute::work($this->work)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  static protected function add($form){
    $ret = [];
    //TODO:日にち指定、月末日指定の場合は別メソッドで対応する
    $calendar_setting = UserCalendarSetting::create([
      'user_id' => $form['user_id'],
      'schedule_method' => $form['schedule_method'],
      'lesson_week_count' => $form['lesson_week_count'],
      'lesson_week' => $form['lesson_week'],
      'from_time_slot' => $form['from_time_slot'],
      'to_time_slot' => $form['to_time_slot'],
      'enable_start_date' => $form['enable_start_date'],
      'enable_end_date' => $form['enable_end_date'],
      'create_user_id' => $form['create_user_id'],
      'setting_id_org' => $form['setting_id_org']
    ]);
    $calendar_setting->memberAdd($form['user_id'], $form['create_user_id'], '主催者');
    $calendar_setting->change($form);
    return $calendar_setting;
  }
  protected function change($form){
    $update_fields = [
      'remark', 'place', 'work', 'enable_end_date', 'lecture_id',
    ];
    $data = [];
    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      $data[$field] = $form[$field];
    }
    $this->update($data);
    return $this;
  }
  public function memberAdd($user_id, $create_user_id, $remark=''){
    if(empty($user_id) || $user_id < 1) return null;
    $member = UserCalendarMemberSetting::create([
        'user_calendar_setting_id' => $this->id,
        'user_id' => $user_id,
        'remark' => $remark,
        'create_user_id' => $create_user_id,
    ]);
    return $member;
  }
  public function setting_to_calendar(){
    //この設定により作成した予定を取得
    $calendars = UserCalendar::where('user_calendar_setting_id', $this->id)->get();
    $_calendars = [];
    foreach($calendars as $calendar){
      $_calendars[$calendar->start_time] = $calendar;
    }
    //この設定の対象日付を取得
    $schedules = $this->get_add_calendar_date();
    $ret = [];
    foreach($schedules as $index => $schedule){
      $start_time = $schedule.' '.$this->from_time_slot;
      if(isset($_calendars[$start_time])){
        $_already_calendar = $_calendars[$start_time];
        if($_already_calendar->end_time == $schedule.' '.$this->to_time_slot
           && $_already_calendar->user_id == $this->user_id){
             //この日付の予定がすでに作成済み
             //開始・終了・主催者が同じ
             continue;
        }
      }
      $ret[] = $this->_setting_to_calendar($schedule);
    }
    return $ret;
  }
  private function _setting_to_calendar($date){
    $default_status = 'fix';
    $form = [
      'status' => $default_status,
      'user_calendar_setting_id' => $this->id,
      'start_time' => $date.' '.$this->from_time_slot,
      'end_time' => $date.' '.$this->to_time_slot,
      'lecture_id' => $this->lecture_id,
      'place' => $this->place,
      'work' => $this->work,
      'exchanged_calendar_id' => 0,
      'remark' => $this->remark,
      'teacher_user_id' => $this->user_id,
      'create_user_id' => 1,
    ];
    $calendar = UserCalendar::add($form);
    foreach($this->members as $member){
      if($this->user_id == $member->user_id) continue;
      //主催者以外を追加
      $calendar->memberAdd($member->user_id, 1, $default_status);
    }
    return $calendar;
  }
  public function get_add_calendar_date($month_week_count=4){
    $ret = [];
    $start_date = date('Y-m-01'); //月初
    if(isset($this->enable_start_date)){
      if(strtotime($start_date) < strtotime($this->enable_start_date)){
        //月初以降の有効開始日の場合、そちらを使う
        $start_date = $this->enable_start_date;
      }
    }
    //3か月後の月末
    $end_date = date('Y-m-t', strtotime(date('Y-m-01') . '+3 month'));
    if(strtotime($end_date) < strtotime($this->enable_end_date)){
      //3か月後月末以前に設定が切れる場合、そちらを使う
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
        $ret[] = $target_date;
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
}
