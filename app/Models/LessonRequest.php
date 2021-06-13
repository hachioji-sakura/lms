<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\Comment;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;
use App\Models\StudentRelation;
use App\Models\LessonRequestDate;
use App\Models\LessonRequestCalendar;
use App\Models\Ask;
use App\Models\Traits\Common;
use App\Models\Traits\Matching;

class LessonRequest extends Model
{
  use Common;
  use Matching;
  protected $table = 'lms.lesson_requests';
  protected $debug = true;
  protected $guarded = array('id');
  public static $rules = array(
  );
  protected $appends = ['status_name', 'created_date', 'updated_date'];

  public $student_schedule = null;
  public $course_minutes = 0;
  public function tags(){
    return $this->hasMany('App\Models\LessonRequestTag', 'lesson_request_id');
  }
  public function request_dates(){
    return $this->hasMany('App\Models\LessonRequestDate', 'lesson_request_id');
  }
  public function user_calendar_settings(){
    return $this->hasMany('App\Models\UserCalendarSetting', 'lesson_request_id');
  }
  public function event(){
    return $this->belongsTo('App\Models\Event', 'event_id');
  }
  public function calendars(){
    return $this->hasMany('App\Models\UserCalendar', 'lesson_request_id');
  }
  public function user(){
    return $this->belongsTo('App\User');
  }
  public function lesson_request_calendars(){
    $calendars = LessonRequestCalendar::searchLessonRequest($this->id);
    return $calendars;
  }
  public function lesson_request_calendar_count($status){
    return $this->lesson_request_calendars()->whereIn('status', $status)->count();
  }
  public function parent(){
    return $this->belongsTo('App\Models\StudentParent', 'create_user_id', 'user_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'user_id', 'user_id');
  }
  public function get_status(){
    $status = $this->status;
    if($this->type=='trial'){
      //体験授業登録状況により、ステータスは変動する
      switch($this->status){
        case "confirm":
        case "fix":
        case "presence":
        case "new":
          $status = 'new';
          if($this->is_confirm_request_lesson()==true){
            $status = 'confirm';
          }
          else if($this->is_all_fix_request_lesson()==true){
            $status = 'fix';
          }
          else if($this->is_presence_request_lesson()==true){
            $status = 'presence';
          }
          break;
      }
      if($this->student->status=='regular'){
        $status = 'complete';
      }
      if($this->status != $status) $this->update(['status' => $status]);
    }
    return $status;
  }
  public function getStatusNameAttribute(){
    return $this->status_name();
  }
  public function status_name(){
    $status = $this->set_status();
    if(app()->getLocale()=='en') return $status;
    if(isset(config('attribute.lesson_request_status')[$status])){
      return config('attribute.lesson_request_status')[$status];
    }
    return "(定義なし)";
  }
  public function getTypeNameAttribute(){
    if(app()->getLocale()=='en') return $this->type;
    if(isset(config('attribute.lesson_request_type')[$this->type])){
      return config('attribute.lesson_request_type')[$this->type];
    }
    return "(定義なし)";
  }
  public function get_request_subject_count(){
    $subject_count = [];
    foreach($this->charge_subject_attributes() as $attribute){
      $subject_code = $attribute->attribute_value;
      $c = intval($this->get_tag_value($subject_code.'_day_count'));
      if($c < 1 ) continue;
      $subject_count[$subject_code] = $c;
    }
    return $subject_count;
  }
  public function get_calendar_subject_count($statuses){
    $calendar_subject_count = [];
    $calendars = $this->lesson_request_calendars()->whereIn('status', $statuses)->get();
    foreach($calendars as $calendar){
      if(!isset($calendar_subject_count[$calendar->subject_code])){
        $calendar_subject_count[$calendar->subject_code]=0;
      }
      $calendar_subject_count[$calendar->subject_code]++;
    }
    return $calendar_subject_count;
  }
  public function get_user_calendar_subject_count(){
    $calendars = UserCalendar::where('lesson_request_id', $this->id)->whereIn('status', ['rest_cacel', 'absence', 'lecture_cancel', 'presence', 'rest', 'fix'])->get();
    $fix_calendar_subject_count = [];
    foreach($calendars as $calendar){
      foreach($calendar->get_tags('charge_subject') as $tag){
        if(!isset($fix_calendar_subject_count[$tag->tag_value])){
          $fix_calendar_subject_count[$tag->tag_value]=0;
        }
        $fix_calendar_subject_count[$tag->tag_value]++;
      }
    }
    return $fix_calendar_subject_count;
  }
  public function set_status(){
    $subject_count = $this->get_request_subject_count();
    $set_calendar_subject_count = $this->get_calendar_subject_count(['fix', 'complete']);
    $fix_calendar_subject_count = $this->get_calendar_subject_count(['fix']);
    $comp_calendar_subject_count = $this->get_calendar_subject_count(['complete']);
    $status = 'new';
    if(count($set_calendar_subject_count)>0) $status = 'confirm';
    $fix_calendar_subject_count = $this->get_user_calendar_subject_count();
    $is_fixed = true;
    $is_schedule_commit = true;

    foreach($subject_count as $subject_code => $c){
      if(!isset($set_calendar_subject_count[$subject_code])){
        //希望科目の予定登録がない
        $is_fixed = false;
      }
      else if($c > $set_calendar_subject_count[$subject_code]){
        //要望科目数＞仮登録授業科目数
        $is_fixed = false;
      }
      if(!isset($comp_calendar_subject_count[$subject_code]) || $c > $comp_calendar_subject_count[$subject_code]){
        //要望科目数＞本登録授業科目数
        $is_schedule_commit = false;
      }

    }
    if($is_schedule_commit==true && $fix_calendar_subject_count==[]){
      $status = 'schedule_commit';
    }
    else if($is_fixed==true){
      $status = 'fix';
    }

    $this->update(['status' => $status]);
    return $status;
  }
  public function add_matching_calendar_for_place($place_id=0){
    //担当講師で先にマッチング処理
    $charge_teachers = $this->student->get_current_charge_teachers();
    $res = $this->_add_matching_calendar_for_place($charge_teachers, $place_id);
    $status = $this->set_status();
    if($status != 'fix' && $status != 'complete'){
      //担当講師以外でマッチング処理
      $charge_teachers = $this->candidate_teachers(0, 1);
      if(count($charge_teachers) >0){
        $res = $this->_add_matching_calendar_for_place($charge_teachers[1], $place_id);
      }
    }
  }
  public function _add_matching_calendar_for_place($charge_teachers, $place_id=0){
    \Log::warning("_add_matching_calendar_for_place(".$place_id.")");
    //①担当講師を取得(直近予定のある講師＞有効なカレンダー設定の講師）
    $subject_teacher_set = [];
    $subject_count = [];
    //②科目にて必要なデータを集計
    //この申し込みが必要としている科目ごとのコマ数
    //科目を担当する講師
    foreach($this->charge_subject_attributes() as $attribute){
      $subject_code = $attribute->attribute_value;
      $c = intval($this->get_tag_value($subject_code.'_day_count'));
      if($c < 1 ) continue;
      $subject_count[$subject_code] = $c;
      foreach($charge_teachers as $teacher){
        $subject_val = $teacher->get_charge_subject_val($subject_code);
        if($subject_val > 0){
          if(!isset($subject_teacher_set[$subject_code]) || (isset($subject_teacher_set[$subject_code]) && $subject_teacher_set[$subject_code]->get_charge_subject_val($subject_code) > $subject_val)){
            $subject_teacher_set[$subject_code] = $teacher;
          }
        }
      }
    }
    echo "-----------------------------<br>\n";
    foreach($subject_teacher_set as $subject_code=>$t){
      echo "科目＝".$subject_code."/講師id=".$t->id.",\n";
    }
    echo "-----------------------------<br>\n";
    foreach($subject_count as $subject_code=>$count){
      echo "科目＝".$subject_code."/希望数=".$count.",\n";
    }
    //登録済みの予定を外す
    $calendars = $this->lesson_request_calendars()->where('status', 'fix')->get();
    echo "登録済みの予定数＝".count($calendars)."<br>\n";
    foreach($calendars as $calendar){
      if(!isset($calendar->subject_code)) continue;
      echo "登録済み予定・科目＝".$calendar->subject_code."<br>\n";
      if(!isset($subject_count[$calendar->subject_code])) continue;
      $subject_count[$calendar->subject_code]--;
    }
    $que = [];
    while(count($subject_count)>0){
      foreach($subject_count as $subject_code => $count){
        if($count>0){
          if(isset($subject_teacher_set[$subject_code])){
            $que[] = $subject_code;
          }
          $subject_count[$subject_code]--;
        }
        if($subject_count[$subject_code]==0){
          unset($subject_count[$subject_code]);
        }
      }
    }
    foreach($que as $i=>$subject_code){
      echo "Que[".$i."][".$subject_code."]<br>\n";
    }
    $date_time_list = $this->get_time_list(1);
    $regular_calendar_dates = [];
    /*TODO
    通常授業は、lesson_request_calendars > user_calendarsのinsert時に、cancel扱いにする
    if($this->has_tag('regular_schedule_exchange', 'true')){
      $calendars = $this->event->get_regular_calendars($this->student->user_id);
      foreach($calendars as $calendar){
        $regular_calendar_dates[] = date('Y-m-d', strtotime($calendar->start_time));
      }
    }
    */
    $i=0;
    while(count($que) > 0){
      //queを参照＞担当講師決定
      echo "que:";
      $subject_code = "";
      foreach($que as $i => $_subject_code){
        echo $i."=".$que[$i]."<br>";
        if(empty($subject_code)) $subject_code = $que[$i];
      }
      echo "<br>";
      $teacher = $subject_teacher_set[$subject_code];
      echo "●que pop[subject=".$subject_code."][講師id=".$teacher->id."]<br>\n";
      $fix_calendars = [];
      $fix_calendar_date = "";
      /*TODO
      通常授業は、lesson_request_calendars > user_calendarsのinsert時に、cancel扱いにする
      if($this->has_tag('regular_schedule_exchange', 'true')){
        echo "〇通常授業振替対象を優先して探索<br>\n";
        foreach($regular_calendar_dates as $d){
          if(!isset($date_time_list[$d])) continue;
          $fix_calendars = $this->pop_calendar($d, $date_time_list[$d], $teacher->id, $place_id, $subject_code);
          if(count($fix_calendars) > 0) {
            $fix_calendar_date = $d;
            break;
          }
        }
      }
      */
      if(count($fix_calendars) < 1){
        echo "〇申込希望日時の範囲で探索<br>\n";
        foreach($date_time_list as $d => $time_list){
          $fix_calendars = $this->pop_calendar($d, $time_list, $teacher->id, $place_id, $subject_code);
          if(count($fix_calendars) > 0) {
            $fix_calendar_date = $d;
            break;
          }
        }
      }
      if(count($fix_calendars) > 0){
        foreach($fix_calendars as $fix_calendar){
          echo "fix_calendar[id=".$fix_calendar->id."][".$fix_calendar->place_floor_id."]<br>";
          for($p=0;$p<count($que);$p++){
            echo "fix_calendar check[id=".$que[$p]."][".$fix_calendar->subject_code."]<br>";
            if($que[$p]==$fix_calendar->subject_code){
              array_splice($que, $p, 1);
              break;
            }
          }
        }
        if(isset($date_time_list[$fix_calendar_date])){
          unset($date_time_list[$fix_calendar_date]);
        }
      }
      else {
        //fiｘ予定が作れないケース
        array_shift($que);
      }
    }
    return true;
  }
  public function pop_calendar($d, $time_list, $teacher_id, $place_id, $subject_code){
    $already_calendar = $this->lesson_request_calendars()->whereIn('status', ['fix', 'complete', 'prohibit'])
                        ->rangeDate($d.' 00:00:00', $d.' 23:59:59')->first();
    if(isset($already_calendar)) return [];
    echo "候補予定[".$d."]を探索<br>\n";
    $this->create_request_calendar(1, $time_list, $teacher_id, $d, $place_id, $subject_code);
    $calendar = $this->request_calendar_review($teacher_id, $d, $subject_code);
    $fix_calendars = [];
    if(isset($calendar[$d]) && count($calendar[$d])>0) $fix_calendars = $this->fix_calendar($calendar[$d]);
    echo "fix_calendars_count=[".count($fix_calendars)."]<br>\n";
    return $fix_calendars;
  }
  public function fix_calendar($calendars){
    echo "------------fix_calendar[".count($calendars)."]----------------------\n<br>";
    $course_minutes = $this->get_tag_value('season_lesson_course');
    $ret = [];
    $t = 0;
    $status = 'fix';
    $d = date('Y-m-d', strtotime($calendars[$t]->start_time));
    $diffrent_place_calendar = LessonRequestCalendar::where('user_id',$calendars[$t]->user_id)
            ->rangeDate($d.' 00:00:00', $d.' 23:59:59')
            ->whereIn('status', ['fix', 'complete'])
            ->where('place_floor_id', '!=', $calendars[$t]->place_floor_id)
            ->first();
    if(isset($diffrent_place_calendar)) $status = 'prohibit';
    if(intval($course_minutes)==120 && isset($calendars[$t]) && isset($calendars[$t+2])){
      if($calendars[$t]->end_time==$calendars[$t+2]->start_time &&
          $calendars[$t]->place_floor_id==$calendars[$t+2]->place_floor_id){
          $calendars[$t]->update(['status' => $status]);
          $calendars[$t+2]->update(['status' => $status]);
          $ret[] = $calendars[$t];
          $ret[] = $calendars[$t+2];
      }
    }
    else {
      $calendars[$t]->update(['status' => $status]);
      $ret[] = $calendars[$t];
    }
    if($status=='fix' && count($ret)>0){
      $this->set_training_calendar($ret);
    }
    return $ret;
  }
  public function getStudentNameAttribute(){
    return $this->student->name();
  }
  public function set_training_calendar($fix_calendars){
    $course_minutes = $this->get_tag_value('season_lesson_course');
    //演習時間は、トータル5時間（300分）- 授業時間
    $training_minutes = 300 - intval($course_minutes);
    $ret = [];
    $create_form = [
      'user_id' => $this->student->user_id,
      'parent_lesson_request_calendar_id' => $fix_calendars[0]->id,
      'lesson_request_date_id' => $fix_calendars[0]->lesson_request_date_id,
      'remark' => '',
      'teaching_type' => 'training',
      'place_floor_id' => $fix_calendars[0]->place_floor_id,
      'subject_code' => '',
      'status' => 'fix',
    ];
    $_start_time = ($fix_calendars[0]->lesson_request_date->day.' '.$fix_calendars[0]->lesson_request_date->from_time_slot);
    $_end_time = ($fix_calendars[0]->lesson_request_date->day.' '.$fix_calendars[0]->lesson_request_date->to_time_slot);
    echo "------------set_training_calendar[".count($fix_calendars)."][".$_start_time."][".$_end_time."]----------------------<br>";

    //①上の時間チェック(from = 要望の開始時刻　、　to = 講習コマの開始時刻）
    $_from = $_start_time.':00';
    $_to = $fix_calendars[0]->start_time;
    $_minutes = strtotime($_to) - strtotime($_from);

    if($_minutes > $training_minutes){
      //空きが多過ぎる場合の調整
      $_from = date('Y-m-d H:i:s', strtotime('-'.$training_minutes.' minutes '.$_to));
    }
    if(strtotime($_from) < strtotime($_start_time)) $_from = $_start_time;
    $_minutes = (strtotime($_to) - strtotime($_from))/60;
    echo "[".$_from."][".$_to."][".$_minutes."][".$training_minutes."]<br>";
    if($_minutes > 30){
      $create_form['start_time'] = $_from;
      $create_form['end_time'] = $_to;
      echo "create before / start_time=[".$create_form['start_time']."]end_time=[".$create_form['end_time']."]<br>";
      $ret[] = LessonRequestCalendar::create($create_form);
      $training_minutes -= $_minutes;
    }
    //②下の時間チェック(from = 講習の終了時刻　、　to = 要望の終了時刻）
    $create_form['parent_lesson_request_calendar_id'] = $fix_calendars[count($fix_calendars)-1]->id;

    $_from = $fix_calendars[count($fix_calendars)-1]->end_time;
    $_to = $_end_time.':00';
    $_minutes = (strtotime($_to) - strtotime($_from))/60;
    echo "[".$_from."][".$_to."][".$_minutes."][".$training_minutes."]<br>";
    if($_minutes > $training_minutes){
      //空きが多過ぎる場合の調整
      $_to = date('Y-m-d H:i:s', strtotime('+'.$training_minutes.' minutes '.$_from));
    }
    if(strtotime($_end_time) < strtotime($_to)) $_to = $_end_time;
    $_minutes = (strtotime($_to) - strtotime($_from))/60;
    echo "[".$_from."][".$_to."][".$_minutes."][".$training_minutes."]<br>";

    if($_minutes > 30){
      $create_form['start_time'] = $_from;
      $create_form['end_time'] = $_to;
      echo "create after / start_time=[".$create_form['start_time']."]end_time=[".$create_form['end_time']."]<br>";
      $ret[] = LessonRequestCalendar::create($create_form);
    }
    return $ret;
  }
  public function get_teacher_request_dates_schedule($teacher_id, $target_day=''){
    \Log::warning("get_teacher_request_dates_schedule(".$teacher_id.")(".$target_day.")");
    $teacher = Teacher::find($teacher_id);
    if(!isset($teacher)) return null;
    $dates = $this->request_dates;
    if(!empty($target_day)){
      $dates = $dates->where('day',$target_day);
    }
    foreach($dates->sortBy('sort_no') as $d){
      if(!empty($target_day) && $d->day!=$target_day) continue;
      $calendars[$d->day] = UserCalendar::findUser($teacher->user_id)
                      ->findStatuses(['fix', 'confirm', 'new'])
                      ->searchDate($d->from_datetime, $d->to_datetime)
                      ->orderBy('start_time')
                      ->get();
   }
    return $calendars;
  }
  private function create_request_calendar($lesson, $time_list, $teacher_id, $target_day, $place_id=0, $subject_code=""){
    \Log::warning("create_request_calendar(".$lesson.")(".$teacher_id.")(".$target_day.")(".$place_id.")(".$subject_code.")");
    /*create_request_calendar
    過ぎた予定の追加を無効にする
    if(strtotime("now") > strtotime($target_day)){
      return [];
    }
    */
    $w = date('w', strtotime($target_day));
    $week = ["sun", "mon", "tue", "wed", "thi", "fri", "sat"];
    $lesson_week = $week[$w];
    $teacher = Teacher::find($teacher_id);
    $lesson_request_date = $this->request_dates->where('day', $target_day)->first();
    //$updated_at = LessonRequestCalendar::where('lesson_request_date_id', $lesson_request_date->id)->where('user_id', $teacher->user_id)->max('updated_at');

    $is_create = false;
    $course_minutes = intval($this->course_minutes);
    $_enable_times = null;
    $slot_unit_minutes = 30;
    if($this->type=='trial'){
      $slot_unit_minutes = 10;
      if($course_minutes > 60) $course_minutes = 60;
      //塾以外の体験授業は、すべて30分
      if($lesson != 1 && $course_minutes > 30) $course_minutes=30;
      //講師の体験授業希望日時を取得
      $_enable_times = $teacher->user->get_trial_enable_times($slot_unit_minutes);
      if(isset($_enable_times[$lesson_week])) $_enable_times = $_enable_times[$lesson_week];
    }
    else if($this->type=='season_lesson'){
      $course_minutes = $this->get_tag_value('season_lesson_course');
      if($course_minutes==120) $course_minutes = 60;
      $teacher_lesson_request = LessonRequest::where('user_id', $teacher->user_id)->first();
      if(!isset($teacher_lesson_request)) return false;
      //講師の講習希望日時を取得
      $teacher_lesson_request_date = $teacher_lesson_request->request_dates->where('day', $target_day)->first();
      if(!isset($teacher_lesson_request_date)) return false;
      $_enable_times = $teacher_lesson_request_date->get_time_slots();
    }
    $minute_count = intval($course_minutes / $slot_unit_minutes);
    //if($updated_at==null || (strtotime($this->updated_at)  > strtotime($updated_at) && strtotime($teacher->updated_at) > strtotime($updated_at))){
      $is_create = true;
      //更新すべき状況
      if(!isset($lesson_request_date)) return false;
      $place_floor = Place::find($place_id)->floors()->first();
      $create_form = [
        'user_id' => $teacher->user_id,
        'lesson_request_date_id' => $lesson_request_date->id,
        'remark' => '',
        'teaching_type' => 'season',
        'place_floor_id' => $place_floor->id,
        'subject_code' => $subject_code,
      ];
      foreach($time_list as $i => $_time){
        $create_form['start_time'] = $_time['start_time'];
        $create_form['end_time'] = $_time['end_time'];
        $c = LessonRequestCalendar::where('lesson_request_date_id', $create_form['lesson_request_date_id'])
                ->where('place_floor_id', $create_form['place_floor_id'])
                ->where('subject_code', $create_form['subject_code'])
                ->where('start_time', $create_form['start_time'])
                ->where('end_time', $create_form['end_time'])->first();
        if(!isset($c)){
          //希望日時範囲内の予定をすべて作成する
          $c = LessonRequestCalendar::create($create_form);
        }
      }
    //}
    //申し込みの希望日時範囲内の既存予定を取得
    $teacher_calendars = UserCalendar::findUser($teacher->user_id)
                    ->findStatuses(['fix', 'confirm', 'new'])
                    ->orderBy('start_time')
                    ->get();
    $teacher_lesson_request_calendars = LessonRequestCalendar::where('user_id', $teacher->user_id)
                    ->findStatuses(['fix', 'complete'])
                    ->orderBy('start_time')
                    ->get();
    foreach($teacher_lesson_request_calendars as $c){
      \Log::warning("teacher_lesson_request_calendars=".$c->id);
    }
    //LessonRequestCalendar::whereIn('id', $lesson_request_date->calendars()->pluck('id')->toArray())->update(['status' => 'new']);
    foreach($lesson_request_date->calendars()->get() as $calendar){
      $matching_result = "";
      $status = $calendar->status;
      if($calendar->status=='new') $status = 'free';
      //講師の体験授業可能曜日・空き時間をチェック
      \Log::warning("create_request_calendar:講師の体験授業可能曜日・空き時間をチェック");
      if(isset($_enable_times)){
        $find_start = false;
        $from = date('Hi', strtotime($calendar->start_time));
        $to = date('Hi', strtotime($calendar->end_time));
        $c = 0;
        foreach($_enable_times as $key => $val){
          $find_end = true;
          if($key == $from && $val===true) {
            $find_start = true;
          }
          if($find_start==true){
            if($val !== true){
              break;
            }
            else {
              $c++;
            }
          }
          if($key == $to){
            break;
          }
        }
        if($find_start !== true || $c < $minute_count){
          //体験授業不可能(空き時間が不足）
          $status = "disabled";
          $matching_result = "lack_of_time";
        }
      }

      if(isset($conflict_calendar)) continue;

      if(isset($teacher_calendars) && $status == "free"){
        //講師の現在の授業予定との競合するかチェック
        $matching_result = $this->get_matching_result($calendar, $teacher_calendars, $place_id);
        \Log::warning("create_request_calendar:講師の現在の授業予定との競合するかチェック");
      }

      if(isset($teacher_lesson_request_calendars) && $status == "free"){
        $matching_result = $this->get_matching_result($calendar, $teacher_lesson_request_calendars, $place_id);
        \Log::warning("create_request_calendar:講師の申し込みでfixした予定と競合するかチェック:".$calendar->id.":".$matching_result);
      }

      //競合状況を保存
      if($matching_result=='time_conflict' || $matching_result=='place_conflict'){
        $status = 'disabled';
      }
      \Log::warning("[".$calendar->id."][".$matching_result."][".$calendar->status."][".$status."]");
      $calendar->update(['matching_result' => $matching_result, 'status' => $status]);
    }
    return true;
  }
  public function request_calendar_review($teacher_id, $target_day='', $subject_code=''){
    \Log::warning("request_calendar_review(".$teacher_id.")(".$target_day.")(".$subject_code.")");

    $teacher = Teacher::find($teacher_id);
    //対象の空き予定を取得
    $calendars = LessonRequestCalendar::where('user_id', $teacher->user_id)
                      ->whereIn('lesson_request_date_id', $this->request_dates->pluck('id')->toArray())
                      ->where('status', 'free');
    if(!empty($target_day)){
      $calendars = $calendars->rangeDate($target_day.' 00:00:00',$target_day.' 23:59:59');
    }
    if(!empty($subject_code)){
      $calendars = $calendars->where('subject_code', $subject_code);
    }
    $calendars->update([
      'review_value' => 0,
      'next_calendar_id' => 0,
      'prev_calendar_id' => 0
    ]);
    $calendars = $calendars->get();

    echo "-----------------------------<br>\n";
    echo "講師ＩＤ＝".($teacher_id)."<br>\n";
    echo "対象日＝".($target_day)."<br>\n";
    echo "仮登録予定数＝".count($calendars)."<br>\n";
    echo "-----------------------------<br>\n";
    //予定ありの次の時間の状態によって評価を設定
    $max_review = 0;

    foreach($calendars as $calendar){
      if($this->type=='season_lesson') {
        $review = $this->schedule_review($teacher_id, $calendar, 'lesson_request_calendars');
        if($review > $max_review) $max_review = $review;
      }
      else {
        $review = $this->schedule_review($teacher_id, $calendar);
      }
      if($review > $max_review) $max_review = $review;
    }
    echo "評価値＝".($max_review)."<br>\n";
    echo "-----------------------------<br>\n";

    //優先度の最も高いものから返却する
    $ret = [];
    $calendars = LessonRequestCalendar::where('user_id', $teacher->user_id)
                      ->whereIn('lesson_request_date_id', $this->request_dates->pluck('id')->toArray())
                      ->where('review_value','>=', $max_review)
                      ->where('status', 'free');
    if(!empty($target_day)){
      $calendars = $calendars->rangeDate($target_day.' 00:00:00',$target_day.' 23:59:59');
    }
    if(!empty($subject_code)){
      $calendars = $calendars->where('subject_code', $subject_code);
    }
    $calendars = $calendars->orderBy('start_time')->get();
    foreach($calendars as $calendar){
      $d = date('Y-m-d', strtotime($calendar->start_time));
      if(!isset($ret[$d])) $ret[$d] = [];
      $ret[$d][] = $calendar;
    }
    return $ret;
  }
  private function schedule_review($teacher_id, $target_calendar, $check_model='user_calendars'){
    \Log::warning("schedule_review(".$teacher_id.")");
    if($target_calendar->status!=="free") return false;

    $teacher = Teacher::find($teacher_id);
    $review_value = 0;
    $prev_calendar_id = 0;
    $next_calendar_id = 0;
    $place_floor_id = $target_calendar->place_floor_id;

    //上に隣接する授業設定を取得
    $prev_calendars = UserCalendar::query();
    if($check_model=='lesson_request_calendars'){
      $prev_calendars = LessonRequestCalendar::query();
    }
    $prev_calendars = $prev_calendars->where('user_id', $teacher->user_id)
                      ->findStatuses(['fix', 'confirm'])
                      ->where('start_time', $target_calendar->end_time)
                      ->get();

    foreach($prev_calendars as $calendar){
      foreach($this->get_tags('lesson_place') as $tag){
        //体験希望所在地のフロアと同じ場合隣接とみなす
        if($calendar->is_same_place($tag->tag_value)){
          $prev_calendar_id = $calendar->id;
          $place_floor_id = $calendar->place_floor_id;
        }
        if($prev_calendar_id > 0) break;
      }
      if($prev_calendar_id > 0) break;
    }
/*
TODO:このロジックは再度検討
    //近い日にちであるほど優先度はあがる
    $d = $this->day_diff($target_calendar->start_time);
    //今週= 100、来週=95、再来週=90
    $d = 100 - intval($d/7)*5;
    $review_value+=$d;
*/
    $review_value = 100;
    //下に隣接する授業設定を取得
    $next_calendars = UserCalendar::query();
    if($check_model=='lesson_request_calendars'){
      $next_calendars = LessonRequestCalendar::query();
    }
    $next_calendars = $next_calendars->where('user_id', $teacher->user_id)
                      ->findStatuses(['fix', 'confirm'])
                      ->where('end_time', $target_calendar->start_time)
                      ->get();
    foreach($next_calendars as $calendar){
      $same_place = "";
      foreach($this->get_tags('lesson_place') as $tag){
        if($calendar->is_same_place($tag->tag_value)){
          $next_calendar_id  = $calendar->id;
          $place_floor_id = $calendar->place_floor_id;
        }
        if($next_calendar_id>0) break;
      }
      if($next_calendar_id>0) break;
    }

    $is_adjacent = false;
    //隣接：評価値+2、移動なし：評価値+1、
    if( ($prev_calendar_id>0 && $next_calendar_id>0) ||
         ($prev_calendar_id>0 && $next_calendar_id==0)){
       //１．上下隣接、２．上に隣接し下がない
       $review_value+=3;
       $is_adjacent = true;
    }
    else if($prev_calendar_id==0 && $next_calendar_id>0){
      //３．下に隣接し上がない
      $review_value+=3;
      $is_adjacent = true;
    }
    else {
      //隣接しない
      $d = date('Y-m-d', strtotime($target_calendar->start_time));
      //同日のスケジュール
      $calendars = UserCalendar::query();
      if($check_model=='lesson_request_calendars'){
        $calendars = LessonRequestCalendar::query();
      }
      $calendars = $calendars->where('user_id', $teacher->user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->findStatuses(['fix', 'confirm'])
                        ->get();

      if(count($calendars) < 1){
        //授業なし
        $review_value+=1;
      }
      else {
        $is_move_schedule = true;
        foreach($calendars as $calendar){
          $same_place = "";
          foreach($this->get_tags('lesson_place') as $tag){
            if(!$calendar->is_same_place($tag->tag_value)){
              $is_move_schedule = false;
            }
          }
        }
        if($is_move_schedule===false){
          //授業があり、移動の必要なし
          $review_value+=2;
        }
      }
    }

    //echo $target["start_time"].":".$ret['review']."/(".isset($prev)." | ".isset($next).")".$target["conflict_calendar"]["id"]."<br>";
    if($is_adjacent===true){
      $d = date('Y-m-d', strtotime($target_calendar->start_time));
      //同日のスケジュール
      $calendars = UserCalendar::query();
      if($check_model=='lesson_request_calendars'){
        $calendars = LessonRequestCalendar::query();
      }
      $min_start_time = $calendars->where('user_id', $teacher->user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->where('status', 'fix')
                        ->min('start_time');
      $calendars = UserCalendar::query();
      if($check_model=='lesson_request_calendars'){
        $calendars = LessonRequestCalendar::query();
      }
      $max_end_time = $calendars->where('user_id', $teacher->user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->where('status', 'fix')
                        ->max('end_time');
      $is_inner = true;
      if(strtotime($target_calendar->start_time) < strtotime($min_start_time)){
        $is_inner = false;
      }
      if(strtotime($target_calendar->end_time) > strtotime($max_end_time)){
        $is_inner = false;
      }
      if($is_inner==true){
        //隣接し、かつ滞在時間拡大しない
        $review_value+=1;
      }
    }
    if($target_calendar->review_value < $review_value){
      /*
      echo "-----------------------------<br>\n";
      echo "schedule_review(".$check_model.")<br>\n";
      echo "calendar_id=".$target_calendar->id."\n";
      echo "review_value=".$review_value."\n";
      echo "next_calendar_id=".$next_calendar_id."\n";
      echo "prev_calendar_id=".$prev_calendar_id."\n";
      echo "place_floor_id=".$place_floor_id."\n";
      echo "-----------------------------<br>\n";
      */
      $target_calendar->update([
        'review_value' => $review_value,
        'next_calendar_id' => $next_calendar_id,
        'prev_calendar_id' => $prev_calendar_id,
        'place_floor_id' => $place_floor_id,
      ]);
    }
    return $review_value;
  }

  //体験希望スケジュールと、講師の勤務可能スケジュール・現在のスケジュール設定を参照し、空きに関する情報を取得する
  public function get_match_schedule($teacher_id){
    $teacher = Teacher::find($teacher_id);
    if(!isset($teacher)) return null;
    //１．この体験対象の生徒の希望スケジュールと希望授業時間を取得
    if(empty($this->student_schedule)){
      //兄弟登録された場合は一人目と同一のため、一人目のスケジュールを利用する
      $student = $this->student;
      $this->student_schedule = $student->user->get_lesson_times(10);
    }
    if($this->couser_minutes==0){
      if(!isset($student)) $student = $this->student;
      if($this->type=='season_lesson'){
        $this->course_minutes = intval($this->get_tag_value('season_lesson_course'));
      }
      else {
        $this->course_minutes = intval($this->get_tag_value('course_minutes'));
      }
    }

    //２．講師の勤務可能スケジュール、通常授業スケジュールを取得
    $teacher_enable_schedule = $teacher->user->get_lesson_times(10);
    $detail = [];
    $count = [];
    $student_schedule = [];
    $from_time = "";
    //３．マッチング判定
    foreach($this->student_schedule as $week_day => $week_schedule){
      $detail[$week_day] = [];
      $count[$week_day] = 0;
      $c = 0;
      //曜日別に時間が有効かチェック
      /*
      if(isset($teacher_current_schedule[$week_day])){
        var_dump($teacher_current_schedule[$week_day]);
        echo"<br>";
      }
      */
      $student_schedule[$week_day] = [];
      foreach($week_schedule as $time => $val){
        $data = ["week_day"=>$week_day, "from" => $time, "to"=>"", "status"=>"free", "review"=>0, "place"=>"", "show"=>false];

        //３－１．生徒の希望スケジュールの場合、講師とのスケジュールを判定する
        if($val===false) {
          $data["status"] = "student_ng";
          $student_schedule[$week_day][] = $data;
          continue;
        }
        $is_free = false;
        //３－２．講師にも同じ曜日・時間の希望がある（ベースのシフト希望）
        if(isset($teacher_enable_schedule) && isset($teacher_enable_schedule[$week_day])
          && isset($teacher_enable_schedule[$week_day][$time])){
            $is_free = $teacher_enable_schedule[$week_day][$time];
          if($is_free===false){
            $data["status"] = "teacher_ng";
          }
        }
        //echo "候補:曜日[".$week_day.'][st='.$data["status"].'/is_free=['.$is_free.']]'.$time."<br>";
        //３－３．現状の講師のカレンダー設定とブッキングしたらfalse
        if($is_free===true){
          $f = date('H:i:00', strtotime('2000-01-01 '.$time.'00'));
          $t = date('H:i:00', strtotime('+'.$this->course_minutes.'minute 2000-01-01 '.$time.'00'));
          foreach($teacher->user->calendar_settings as $setting){
            if($setting->lesson_week != $week_day) continue;
            //echo "conflict?:".$week_day.'?='.$setting->lesson_week.'/'.$f."-".$t." / ".$setting->from_time_slot."-".$setting->to_time_slot."<br>";
            if($setting->is_conflict_setting("week", 0 , $week_day,$f,$t)==true){
              //echo "conflict!!<br>";
              if($setting->is_group()==false){
                $is_free = false;
              }
              $data["status"] = "time_conflict";
              $data["conflict_calendar_setting"] = $setting;
              break;
            }
          }
        }
        if($is_free===true){
          //echo"空き[".$week_day."][".$time."][".$this->course_minutes."][".$is_free."]<br>";
          // 空き
          if(empty($from_time)){
            //どこからどこまでの時間が空いているか記録
            $from_time = $time;
          }
          $c+=10;
          $student_schedule[$week_day][] = $data;
        }
        else {
          // 空きがない
          if(!empty($from_time)){
            //直前まで連続していた
            //可能なコマがある場合カウントアップ
            $_slot = floor($c / $this->course_minutes);
            if($_slot > 0){
              $count[$week_day]+=$_slot;
            }

            $detail[$week_day][] = [
              "from" => $from_time,
              "to" => $time,
              "slot" => $_slot
            ];
          }
          $from_time = "";
          $c = 0;
        }
      }
      //最終値の処理
      if(!empty($from_time)){
        $_slot = floor($c / $this->course_minutes);
        if($_slot > 0){
          $count[$week_day]+=$_slot;
        }
        if($time=='2130') $time='2200';
        $detail[$week_day][] = [
          "from" => $from_time,
          "to" => $time,
          "slot" => $_slot
        ];
        $from_time = "";
      }
    }
    //time_slotの評価
    $max_review = 0;
    $minute_count = intval($this->course_minutes / 10);
    foreach($student_schedule as $week_day => $week_schedule){
      for($i=0;$i<count($week_schedule);$i++){
        $c = 0;
        if($week_schedule[$i]["status"]==="free"){
          $d = date('Hi', strtotime('+'.$this->course_minutes.'minute 2000-01-01 '.$student_schedule[$week_day][$i]["from"].'00'));
          $student_schedule[$week_day][$i]["to"] =  $d;
          //隣接チェック
          //直前が埋まっている場合
          $from = substr($student_schedule[$week_day][$i]["from"], 0,2).':'.substr($student_schedule[$week_day][$i]["from"], -2).':00';
          $to = substr($student_schedule[$week_day][$i]["to"], 0,2).':'.substr($student_schedule[$week_day][$i]["to"], -2).':00';
          $review = $this->get_setting_review($teacher->user_id, $week_day, $from, $to);
          if($review['status']=='place_conflict'){
            $student_schedule[$week_day][$i]["status"] = "place_conflict";
          }
          else {
            $student_schedule[$week_day][$i]["review"] = $review['review'];
            $student_schedule[$week_day][$i]["place"] = $review['same_place'];
            if($max_review < $review['review']){
              $max_review = $review['review'];
            }
          }
        }

        //echo "隣接チェック後：[$week_day][$c][".$student_schedule[$week_day][$i]["from"]."][".$student_schedule[$week_day][$i]["status"]."][".$student_schedule[$week_day][$i]["review"]."]<br>";
      }
    }
    //優先度に応じた、設定可能な曜日×開始時間リストを返す
    foreach($student_schedule as $week_day => $week_schedule){
      $c=0;
      for($i=0;$i<count($week_schedule);$i++){
        if($week_schedule[$i]["status"]!=="free") continue;
        $c++;
        if($max_review > 0){
          if($week_schedule[$i]["review"]===$max_review) {
            $student_schedule[$week_day][$i]["show"] = true;
          }
        }
        else {
          $student_schedule[$week_day][$i]["show"] = true;
        }
        //echo "最終候補：".$week_day."[".$student_schedule[$week_day][$i]["from"]."][".$student_schedule[$week_day][$i]["review"]."][".$student_schedule[$week_day][$i]["show"]."]<br>";
      }
    }
    return ["detail" => $detail, "result" => $student_schedule];
  }
  //コマの評価づけ：隣接するスケジュールを優先とする
  private function get_setting_review($user_id, $lesson_week, $from_time_slot, $to_time_slot){
    //echo "get_setting_review[".$lesson_week."][".$from_time_slot."][".$to_time_slot."]<br>";
    $is_place_conflict = false;
    $ret = ['setting'=>null, 'same_place'=>'', 'review'=>0, 'status'=>''];
    //上に隣接する通常授業設定を取得
    $prev_settings = UserCalendarSetting::where('user_id', $user_id)
                      ->where('from_time_slot', $to_time_slot)
                      ->where('lesson_week', $lesson_week)
                      ->get();
    $prev = null;
    foreach($prev_settings as $setting){
      if($setting->is_enable()==false) continue;
      foreach($this->get_tags('lesson_place') as $tag){
        //体験希望所在地のフロアと同じ場合隣接とみなす
        if($setting->is_same_place($tag->tag_value)==true){
          $prev  = $setting;
          $is_place_conflict = false;
          //echo "prev----setting_id[".$setting->id."]=<br>";
          break;
        }
        else {
          $is_place_conflict = true;
        }
        if(isset($prev)) break;
      }
      if(isset($prev)) break;
    }
    $is_place_conflict = false;
    //下に隣接する通常授業設定を取得
    $next_settings = UserCalendarSetting::where('user_id', $user_id)
                      ->where('to_time_slot', $from_time_slot)
                      ->where('lesson_week', $lesson_week)
                      ->get();
    $next = null;
    foreach($next_settings as $setting){
      if($setting->is_enable()==false) continue;
      foreach($this->get_tags('lesson_place') as $tag){
        if($setting->is_same_place($tag->tag_value)==true){
          $next  = $setting;
          $is_place_conflict = false;
          //echo "next----setting_id[".$setting->id."]=<br>";
          break;
        }
        else {
          $is_place_conflict = true;
        }
        if(isset($next)) break;
      }
      if(isset($next)) break;
    }

    $is_adjacent = false;
    if($is_place_conflict==true){
      $ret['review'] = -1;
      $ret['status'] = "place_conflict";
    }
    else if( (isset($prev) && isset($next)) ||
         (isset($prev) && !isset($next) && count($next_settings)<1)){
       //１．上下隣接、２．上に隣接し下がない
       $ret['setting'] = $prev;
       $ret['same_place'] = $prev->place_floor_id;
       $ret['review']+=3;
       $is_adjacent = true;
    }
    else if(!isset($prev) && isset($next) && count($prev_settings)<1){
      //３．下に隣接し上がない
      $ret['setting'] = $next;
      $ret['same_place'] = $next->place_floor_id;
      $ret['review']+=3;
      $is_adjacent = true;
    }

    if($is_adjacent===true){
      //同日のスケジュール
      $min_from_time_slot = UserCalendarSetting::where('user_id', $user_id)
                        ->where('lesson_week', $lesson_week)
                        ->where('status', 'fix')
                        ->min('from_time_slot');

      $max_to_time_slot = UserCalendarSetting::where('user_id', $user_id)
                        ->where('lesson_week', $lesson_week)
                        ->where('status', 'fix')
                        ->max('to_time_slot');
      $is_inner = true;
      if(strtotime("2000-01-01 ".$from_time_slot) < strtotime("2000-01-01 ".$min_from_time_slot)){
        $is_inner = false;
      }
      if(strtotime("2000-01-01 ".$to_time_slot) > strtotime("2000-01-01 ".$max_to_time_slot)){
        $is_inner = false;
      }
      if($is_inner==true){
        //隣接し、かつ滞在時間拡大しない
        $ret['review']+=1;
      }
    }
    return $ret;
  }
  public function estimate_send(){
    
  }
}
