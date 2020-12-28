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
use App\Models\Ask;
use App\Models\Traits\Common;

class LessonRequest extends Model
{
  use Common;
  protected $table = 'lms.lesson_requests';
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
  public function parent(){
    return $this->belongsTo('App\Models\StudentParent', 'create_user_id', 'user_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'user_id', 'user_id');
  }
  /**
   *　スコープ：ステータス
   */
   public function scopeFindStatuses($query, $vals, $is_not=false)
   {
     return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
   }

  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeSearchWord($query, $word)
  {
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('remark','like', $_like);
      }
    });
    return $query;
  }
  public function is_exist_calendar_settings(){
    $calendar_settings = $this->student->user->get_enable_lesson_calendar_settings();
    foreach($calendar_settings as $lesson => $d0){
      foreach($d0 as $schedule_method => $d1){
        foreach($d1 as $lesson_week => $settings){
          foreach($settings as $setting){
            return true;
          }
        }
      }
    }
    return false;
  }
  public function is_all_fix_request_lesson(){
    $is_find = false;
    foreach($this->calendars as $calendar){
      if($calendar->status=='cancel') continue;
      if($calendar->status=='new') return false;
      if($calendar->status=='confirm') return false;
      if($calendar->status=='fix') $is_find = true;
    }
    return $is_find;
  }
  public function is_confirm_request_lesson(){
    foreach($this->calendars as $calendar){
      if($calendar->status=='cancel') continue;
      if($calendar->status=='new') return true;
      if($calendar->status=='confirm') return true;
    }
    return false;
  }
  public function is_presence_request_lesson(){
    if(count($this->calendars) < 1) return false;
    $is_find = false;
    foreach($this->calendars as $calendar){
      if($calendar->status=='presence') $is_find = true;
      if($calendar->is_last_status()==false) return false;
    }
    return $is_find;
  }
  public function is_regular_schedule_fix(){
    if(count($this->user_calendar_settings) < 1) return false;
    foreach($this->user_calendar_settings as $setting){
      if($setting->is_enable()==false) continue;
      if($setting->status!='fix') return false;
    }
    return true;
  }
  public function is_request_lesson_complete(){
      switch($this->status){
        case "confirm":
        case "fix":
        case "cancel":
        case "new":
        case "presence":
          return false;
      }
      return true;
  }
  public function getStatusNameAttribute(){
    return $this->status_name();
  }
  public function status_name(){
    if(app()->getLocale()=='en') return $this->status;
    if(isset(config('attribute.lesson_request_status')[$this->status])){
      return config('attribute.lesson_request_status')[$this->status];
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
  /*
  public function trial_start_end_time($i){
    $ret = $this->dateweek_format($this["trial_start_time".$i]).date(' H:i',  strtotime($this["trial_start_time".$i])).'～'.date(' H:i',  strtotime($this["trial_end_time".$i]));
    return $ret;
  }
  */
  public function entry_contact_send_date(){
      return $this->get_ask_created_date('hope_to_join');
  }
  public function entry_guidanced_send_date(){
      return $this->get_ask_created_date('agreement');
  }
  public function get_ask_created_date($type){
    $a = Ask::where('type', $type)
      ->where('target_model', 'lesson_requests')
      ->where('target_model_id', $this->id)
      ->first();
      if(!isset($a)) return "-";
    return $this->dateweek_format($a->created_at);
  }
  public function get_subject($is_juken=false){
    $subject = [];
    $filter_val = 1;
    if($is_juken==true) $filter_val = 10;
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > $filter_val){
          $subject[]= $tag->keyname();
        }
      }
    }
    return $subject;
  }
  /**
   * 体験授業申し込み登録
   */
  static public function entry($form){
    //１．保護者情報登録：無名のアカウント＋電話番号、メールアドレスを保存
    //同じ送信内容の場合は登録しない
    $parent = null;
    if(!empty($form['student_parent_id'])){
      $parent = StudentParent::where('id', $form['student_parent_id'])->first();
      $form["create_user_id"] = $parent->user_id;
    }
    if($parent==null){
      $form["accesskey"] = '';
      //TODO デフォルトパスワード
      $form["password"] = 'sakusaku';
      $form["kana_last"] = $form["parent_kana_last"];
      $form["kana_first"] = $form["parent_kana_first"];
      $form["name_last"] = $form["parent_name_last"];
      $form["name_first"] = $form["parent_name_first"];
      $parent = StudentParent::entry($form);
      $form["create_user_id"] = $parent->user_id;
      $parent->profile_update($form);
    }
    if($parent==null) return null;

    $student = null;
    if(!empty($form['student_id'])){
      $student = Student::where('id', $form['student_id'])->first();
    }
    if($student==null) {
      $form["kana_last"] = $form["student_kana_last"];
      $form["kana_first"] = $form["student_kana_first"];
      $form["name_last"] = $form["student_name_last"];
      $form["name_first"] = $form["student_name_first"];
      //２．生徒情報登録：氏名・カナ・学年・学校名
      $student = $parent->brother_add($form, 1);
    }

    $ret = [];

    //登録申し込み情報
    $lesson_request = LessonRequest::where('create_user_id', $parent->user_id)
    ->where('user_id', $student->user_id)
    ->where('status', '!=' ,'cancel')
    ->first();

    if(!isset($lesson_request)){
      //同じ対象生徒の内容の場合は(cancel以外)登録しない
      $lesson_request = LessonRequest::create([
        'type' => $form['type'],
        'create_user_id' => $parent->user_id,
        'user_id' => $student->user_id
      ]);
    }

    //申し込み情報更新
    //同じ送信内容の場合は、申し込み情報のみ更新する
    unset($form['name_last']);
    unset($form['name_first']);
    unset($form['kana_last']);
    unset($form['kana_first']);
    unset($form['birth_day']);
    unset($form['gender']);
    unset($form['grade']);
    unset($form['school_name']);

    $lesson_request->change($form);
    return $lesson_request;
  }
  static public function add($form){
    $event_user = EventUser::find($form['event_user_id']);
    $form['create_user_id'] = $event_user->user_id;
    if($event_user->user->get_role()=='student'){
      $relation = StudentRelation::where('student_id', $event_user->user->student->id)->first();
      $form['create_user_id'] = $relation->parent->user_id;
    }
    //登録申し込み情報
    $lesson_request = LessonRequest::where('user_id', $event_user->user_id)
    ->where('event_id', $event_user->event->id)
    ->where('status', '!=' ,'cancel')
    ->first();

    if(!isset($lesson_request)){
      //同じ対象生徒の内容の場合は(cancel以外)登録しない
      $lesson_request = LessonRequest::create([
        'type' => $form['type'],
        'user_id' => $event_user->user_id,
        'event_id' => $event_user->event->id,
        'create_user_id' => $form['create_user_id']
      ]);
    }
    $lesson_request->change($form);
    return $lesson_request;
  }
  public function change($form){
    $fields = ['remark'];
    $data = [];
    foreach($fields as $field){
      if(!empty($form[$field])) $data[$field] = $form[$field];
    }
    $this->update($data);
    LessonRequestDate::where('lesson_request_id', $this->id)->delete();
    foreach($form['request_dates'] as $datetime){
      if(empty($datetime['sort_no'])) $datetime['sort_no'] = 1;
      LessonRequestDate::create(['lesson_request_id' => $this->id,
                                'day' => date('Y-m-d', strtotime($datetime['from_datetime'])),
                                'from_time_slot' => date('H:i', strtotime($datetime['from_datetime'])),
                                'to_time_slot' => date('H:i', strtotime($datetime['to_datetime'])),
                                'sort_no' => $datetime['sort_no']
      ]);
    }

    $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'english_talk_lesson'];
    $tag_names[] ='entry_milestone';
    $tag_names[] ='howto';
    //通塾可能曜日・時間帯タグ
    $lesson_weeks = config('attribute.lesson_week');
    foreach($lesson_weeks as $lesson_week=>$name){
      $tag_names[] = 'lesson_'.$lesson_week.'_time';
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && count($form[$tag_name])>0){
        LessonRequestTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
      else {
        LessonRequestTag::clearTags($this->id, $tag_name);
      }
    }
    $tag_names = ['piano_level', 'english_teacher', 'lesson_week_count', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes'
      ,'entry_milestone_word','howto_word', 'course_type'
      ,'season_lesson_course', 'regular_schedule_exchange', 'hope_timezone'
      ,'school_vacation_start_date', 'school_vacation_end_date', 'installment_payment'
    ];
    //科目タグ
    foreach($this->charge_subject_attributes() as $attribute){
      $tag_names[] = $attribute->attribute_value.'_day_count';
      $tag_names[] = $attribute->attribute_value.'_level';
    }
    foreach($tag_names as $tag_name){
      if(empty($form[$tag_name])) $form[$tag_name] = '';
      LessonRequestTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
    }

    $this->write_comment($this->type);
    if($this->type=='trial'){
      $this->student->profile_update($form);
    }
  }
  public function remark_full(){
    $ret = "";
    $is_other = false;
    if(count($this->get_tags('entry_milestone')) > 0){
      $ret .= "■".__('labels.entry_milestone')."\n";
      foreach($this->get_tags('entry_milestone') as $tag){
        if($tag->tag_value=='other'){
          $is_other = true;
          continue;
        }
        $ret .= "・".$tag->tag_name."\n";
      }
      if($is_other && empty($this->get_tag_value('entry_milestone_word'))){
        $ret .= "・".$this->get_tag_value('entry_milestone_word')."\n";
      }
    }
    if(!empty($this->remark)){
      $ret .= "■".__('labels.other')."\n";
      $ret .= $this->remark;
    }
    return $ret;
  }
  public function get_calendar(){
    //キャンセルではない、この体験授業生徒の予定
    $calendar = UserCalendar::findUser($this->student->user_id)->get();
    return $calendar;
  }
  public function get_calendar_settings(){
    $user_calendar_settings = UserCalendarSetting::findUser($this->student->user_id)->orderByWeek()->get();
    return $user_calendar_settings;
  }
  public function request_to_calendar($form){
    $this->update(['status' => 'confirm']);
    $teacher = Teacher::where('id', $form['teacher_id'])->first();
    //$calendar = $this->get_calendar();
    $calendar = null;
    //１申し込み複数授業予定のケースもある
    $course_minutes = intval(strtotime($form['end_time']) - strtotime($form['start_time']))/60;
    $calendar_form = [
      'start_time' =>  $form["start_time"],
      'end_time' =>  $form["end_time"],
      'lesson_request_id' => $this->id,
      'place_floor_id' => $form['place_floor_id'],
      'lesson' => $form['lesson'],
      'course_type' => $form['course_type'],
      'course_minutes' => $course_minutes,
      'remark' => $this->remark_full(),
      'matching_decide_word' => $form['matching_decide_word'],
      'matching_decide' => $form['matching_decide'],
      'exchanged_calendar_id' => 0,
      'target_user_id' => $teacher->user_id,
    ];
    $charge_student_form = [
      'teacher_id' => $teacher->id,
    ];
    $common_fields = ['create_user_id', 'charge_subject', 'english_talk_lesson', 'piano_lesson', 'kids_lesson'];
    foreach($common_fields as $field){
      if(isset($form[$field])){
        $calendar_form[$field] = $form[$field];
        $charge_student_form[$field] = $form[$field];
      }
    }
    if(isset($form['calendar_id']) && $form['calendar_id']>0){
      $calendar = UserCalendar::where('id', $form['calendar_id'])->first();
      if(!isset($calendar)){
        \Log::error("存在しないカレンダーへの参加者追加");
        return $this->error_response("存在しないカレンダーへの参加者追加(id=".$this->id.")", "LessonRequest::_to_calendar");
      }
    }
    else {
      $res = UserCalendar::add($calendar_form);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $calendar = $res['data'];
    }
    if($calendar!=null){
      $calendar->memberAdd($this->student->user_id, $form['create_user_id']);
      $charge_student_form['student_id'] = $this->student->id;
      ChargeStudent::add($charge_student_form);

      if(isset($form['send_mail']) && $form['send_mail']=='teacher')  $calendar->register_mail([], $form['create_user_id']);
    }
    return $this->api_response(200,"","",$calendar);
  }
  public function candidate_teachers($teacher_id, $lesson){
    if($teacher_id > 0 && $lesson > 0){
      return $this->_candidate_teachers($teacher_id, $lesson);
    }
    $ret = [];
    $lessons = $this->tags->where('tag_key', 'lesson');
    foreach($lessons as $lesson){
      $_candidate_teachers = $this->_candidate_teachers($teacher_id, intval($lesson->tag_value));
      if(isset($_candidate_teachers) && count($_candidate_teachers) > 0){
        $ret[$lesson->tag_value] = $_candidate_teachers;
      }
    }
    return $ret;
  }
  public function _candidate_teachers($teacher_id=0, $lesson=0){

    $_subjects = [];
    $kids_lesson = [];
    $english_talk_lesson = [];
    $course_minutes = 0;

    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 1){
          //希望科目のタグのみ集める
          $_subjects[$tag->tag_key] = $tag;
        }
      }
      if($tag->tag_key==='course_minutes'){
        $course_minutes = intval($tag->tag_value);
      }
      if($tag->tag_key==='kids_lesson'){
        $kids_lesson[] = $tag->tag_value;
      }
      if($tag->tag_key==='english_talk_lesson'){
        $english_talk_lesson[] = $tag->tag_value;
      }
    }
    $teachers = Teacher::findStatuses(["regular"]);
    if($lesson > 0){
      $teachers = $teachers->hasTag('lesson', $lesson);
      if($lesson===1){
        //塾の場合、担当可能な科目がある講師
        $teachers = $teachers->chargeSubject($_subjects);
      }
      else if($lesson===2){
        //英会話の場合、希望レッスンが担当可能な講師
        $teachers = $teachers->hasTags('english_talk_lesson', $english_talk_lesson);
      }
      else if($lesson===4){
        //習い事の場合、希望レッスンが担当可能な講師
        $teachers = $teachers->hasTags('kids_lesson', $kids_lesson);
      }
    }
    //在籍する講師を取得
    if($teacher_id > 0){
      //講師選択済みの場合
      //対象選択条件は科目が担当可能なので、id指定は後から行う必要がある
      $teachers = $teachers->where('id', $teacher_id);
    }
    $teachers = $teachers->get();

    //この申し込みの希望日時より、30分ごとの開始時間から授業時間までのslotを作成
    $time_lists = [];
    foreach($this->request_dates->sortBy('sort_no') as $d){
      $time_lists[$d->day] = $this->get_time_list($d->from_datetime, $d->to_datetime, $lesson);
    }
    $ret = [];
    foreach($teachers as $teacher){
      $enable_point = 0;
      $disable_point = 0;
      $disable_subject = [];
      $enable_subject = [];

      if($lesson==1){
        $charge_subjects = $teacher->get_charge_subject();
        //塾の場合、担当可能、不可能な科目の情報セットを作る
        foreach($_subjects as $tag_key  => $tag){
          $tag_val = intval($tag->tag_value);
          $tag_keyname = $tag->keyname();
          $tag_name = $tag->name();
          if(isset($charge_subjects[$tag_key])){
            if($tag_val < intval($charge_subjects[$tag_key])){
              //対応可能、希望を上回る
              $enable_subject[$tag_key] = [
                "subject_key" => str_replace('_level', '', $tag_key),
                "subject_name" => $tag_keyname,  //科目名
                "level_name" => $tag_name, //補習可能、受験可能など
                "style" => "primary",
              ];
              $enable_point +=2;
            }
            else if($tag_val == intval($charge_subjects[$tag_key])){
              //対応可能
              $enable_subject[$tag_key] = [
                "subject_key" => str_replace('_level', '', $tag_key),
                "subject_name" => $tag_keyname,  //科目名
                "level_name" => $tag_name, //補習可能、受験可能など
                "style" => "secondary",
              ];
              $enable_point++;
            }
            else {
              //対応不可能：希望を下回る
              $disable_subject[$tag_key] = [
                "subject_key" => str_replace('_level', '', $tag_key),
                "subject_name" => $tag_keyname,  //科目名
                "level_name" => $tag_name, //補習可能、受験可能など
                "style" => "secondary",
              ];
              $disable_point++;
            }
          }
          else {
            //対応不可能
            $disable_subject[$tag_key] = [
              "subject_key" => str_replace('_level', '', $tag_key),
              "subject_name" => $tag_keyname,  //科目名
              "level_name" => $tag_name, //補習可能、受験可能など
              "style" => "danger",
              "parent_key" => "",
            ];
            $disable_point++;
          }
        }
      }
      else if($lesson==3){
        //ピアノの場合特に判断基準なし
        $enable_subject['piano'] = [
          "subject_key" => 'piano',
          "subject_name" => 'ピアノ',  //科目名
          "level_name" => '',
          "style" => "primary",
        ];
        $enable_point ++;
      }
      else if($lesson==4 || $lesson==2){
        //その習い事担当できるか
        $key_name = 'kids_lesson';
        if($lesson==2){
          $key_name = 'english_talk_lesson';
        }
        foreach($this->tags as $tag){
          if($tag->tag_key !== $key_name) continue;
          //echo "[".$key_name."][".$tag->tag_value."]";
          if($teacher->user->has_tag($key_name, $tag->tag_value)){
            //対応可能
            $enable_subject[$tag->tag_value] = [
              "subject_key" => $tag->tag_value,
              "subject_name" => $tag->name(),
              "style" => "secondary",
            ];
            $enable_point++;
          }
          else {
            //対応不可能
            $disable_subject[$tag->tag_value] = [
              "subject_key" => $tag->tag_value,
              "subject_name" => $tag->name(),
              "style" => "danger",
            ];
            $disable_point++;
          }
        }
      }

      if($enable_point > 0){
        $match_schedule = $this->get_match_schedule($teacher);
        if($match_schedule['all_count'] >= 0){
          $teacher->enable_point = $enable_point;
          $teacher->disable_point = $disable_point;
          $teacher->subject_review = 'part';
          if($disable_point < 1) $teacher->subject_review = 'all';
          $teacher->enable_subject = $enable_subject;
          $teacher->disable_subject = $disable_subject;

          $teacher->match_schedule = $match_schedule;
          $calendars = [];
          foreach($this->request_dates->sortBy('sort_no') as $d){
            $calendars[$d->day] = UserCalendar::findUser($teacher->user_id)
                            ->findStatuses(['fix', 'confirm', 'new'])
                            ->searchDate($d->from_datetime, $d->to_datetime)
                            ->orderBy('start_time')
                            ->get();
          }

          $teacher->calendars = $calendars;
          $request_lessons = [];
          foreach($time_lists as $date => $time_list){
            $request_lessons = array_merge($request_lessons,
              $this->get_time_list_free($lesson, $time_list, $teacher->user_id, $date, $calendars[$date], $date)
            );
          }
          $request_lessons = $this->get_time_list_review($teacher->user_id, $request_lessons);
          $teacher->request_lessons = $request_lessons;
          $ret[] = $teacher;
        }

      }
    }
    return $ret;
  }
  public function to_calendar_setting($form, $calendar_id){
    $calendar = UserCalendar::where('id', $calendar_id)->first();

    $trial_details = $this->details($form['create_user_id']);
    $teacher = Teacher::where('id', $form['teacher_id'])->first();

    $calendar_setting = [
      'user_id' => $teacher->user_id,
      'trial_id' => $this->id,
      'schedule_method' => 'week',
      'lesson_week_count' => 0,
      'lesson_week' => $form['lesson_week'],
      'from_time_slot' => $form['from_time_slot'],
      'to_time_slot' => $form['to_time_slot'],
      'place_floor_id' => $form['place_floor_id'],
      'remark' => '',
      'lesson' => $calendar->get_tag('lesson')->tag_value,
      'create_user_id' => $form['create_user_id'],
      'enable_start_date' => $this->schedule_start_hope_date
    ];
    $update_fields = [
      'start_hours', 'start_minutes',
      'course_minutes', 'lesson_week', 'create_user_id', 'course_type', 'enable_start_date', 'enable_end_date', 'lecture_id',
      'charge_subject', 'kids_lesson', 'english_talk_lesson', 'piano_lesson'
    ];
    foreach($update_fields as $field){
      if(!isset($form[$field])) continue;
      $calendar_setting[$field] = $form[$field];
    }
    $setting = null;
    if($form['action'] == 'new'){
      $res = UserCalendarSetting::add($calendar_setting);
      if($this->is_success_response($res)){
        $setting = $res['data'];
      }
    }
    else {
      $setting = UserCalendarSetting::where('id', $form['calendar_setting_id'])->first();
    }
    if(isset($setting)){
      $setting->memberAdd($this->student->user_id, $form['create_user_id']);
    }
    return $this->api_response(200, '', '', $setting);
  }
  private function get_time_list($trial_start_time, $trial_end_time, $lesson){
    $_start = $trial_start_time;
    $time_list = [];
    if(empty($trial_start_time) || empty($trial_end_time)){
      return $time_list;
    }
    if(strtotime("now") > strtotime($trial_start_time)){
      return [];
    }
    //体験授業は、30分、60分の2択
    $course_minutes = 30;
    if($lesson==1) $course_minutes = 60;
    //１０分ずらしで、授業時間分の範囲を配列に設定する
    while(1){
      $_end = date("Y-m-d H:i:s", strtotime("+".$course_minutes." minute ".$_start));
      if(strtotime($_end) > strtotime($trial_end_time)){
        break;
      }
      $_duration = date('H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $status = "free";
      foreach($this->get_calendar() as $calendar){
        if($calendar->is_enable_status($calendar->status)==false) continue;
        //この時間範囲にて体験授業がすでに登録されている場合は、無効
        if($calendar->is_conflict($_start, $_end)){
          $status = "trial_calendar_conflict";
          break;
        }
      }
      $time_list[]=[
        'start_time' => $_start, 'end_time' => $_end, 'status' => $status, 'duration' => $_duration,
        'work_time_from' => date('Hi', strtotime($_start)),
        'work_time_to' => date('Hi', strtotime($_end)),
      ];
      $_start = date("Y-m-d H:i:s", strtotime("+10 minute ".$_start));
    }
    return $time_list;
  }
  private function get_time_list_free($lesson, $time_list, $teacher_user_id, $trial_date, $now_calendars, $remark=""){
    /*
    if(strtotime("now") > strtotime($trial_date)){
      return [];
    }
    */
    $teacher = Teacher::where('user_id', $teacher_user_id)->first();

    $w = date('w', strtotime($trial_date));
    $week = ["sun", "mon", "tue", "wed", "thi", "fri", "sat"];
    $lesson_week = $week[$w];
    $trial_enable_times = $teacher->user->get_trial_enable_times(10);
    if(isset($trial_enable_times[$lesson_week])) $trial_enable_times = $trial_enable_times[$lesson_week];
    else $trial_enable_times = null;
    //講師の対象日のカレンダーを取得
    $_time_list = $time_list;
    $course_minutes = intval($this->course_minutes);
    if($course_minutes > 60) $course_minutes = 60;
    //塾以外の体験授業は、すべて30分
    if($lesson != 1 && $course_minutes > 30) $course_minutes=30;
    $minute_count = intval($course_minutes / 10);
    foreach($_time_list as $i => $_time){
      $_time_list[$i]["conflict_calendar"] = null;
      $_time_list[$i]["is_time_conflict"] = false;
      $_time_list[$i]["is_place_conflict"] = false;
      $_time_list[$i]["free_place_floor"] = "";
      $_time_list[$i]["review"] = 0;
      $_time_list[$i]["remark"]=$remark;;
      //講師の体験授業可能曜日・時間をチェック
      if(isset($trial_enable_times)){
        $find_start = false;
        $from = $_time_list[$i]['work_time_from'];
        $to = $_time_list[$i]['work_time_to'];
        $c = 0;
        foreach($trial_enable_times as $key => $val){
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
          //体験授業不可能
          $_time_list[$i]["status"] = "disabled";
        }
      }

      if(isset($now_calendars) && $_time_list[$i]["status"] == "free"){
        //講師の現在の授業予定との競合するかチェック
        foreach($now_calendars as $now_calendar){
          if($_time_list[$i]['is_time_conflict']===false){
            if($now_calendar->is_conflict($_time['start_time'], $_time['end_time'])){
              //時間が競合した場合
              $_time_list[$i]['conflict_calendar'] = $now_calendar;
              $_time_list[$i]['is_time_conflict'] = true;
            }
          }
          if($_time_list[$i]['is_time_conflict']===false && $_time_list[$i]['is_place_conflict']===false){
            $_free_place = "";
            $is_place_conflict = true;
            foreach($this->get_tags('lesson_place') as $tag){
              if(!$now_calendar->is_conflict($_time['start_time'], $_time['end_time'], $tag->tag_value)){
                //場所の競合もなし=選択候補
                $is_place_conflict = false;
                if($now_calendar->is_same_place($tag->tag_value)){
                  $_free_place = $now_calendar->place_floor_id;
                }
              }
            }
            $_time_list[$i]['is_place_conflict'] = $is_place_conflict;
            if($is_place_conflict===true){
              $_time_list[$i]['conflict_calendar'] = $now_calendar;
            }
            else {
              //空いている
              $_time_list[$i]['free_place_floor'] = $_free_place;
            }
          }
        }
      }
      //競合状況を保存
      if($_time_list[$i]["is_time_conflict"]){
        $_time_list[$i]["status"] = "time_conflict";
        if($_time_list[$i]["is_place_conflict"]){
          $_time_list[$i]["status"] = "time_place_conflict";
        }
      }
      else if($_time_list[$i]["is_place_conflict"]){
        $_time_list[$i]["status"] = "place_conflict";
      }
    }
    return $_time_list;
  }
  private function get_time_list_review($user_id, $_time_list){
    $primary_count = 0;
    $secondary_count = 0;
    //予定ありの次の時間の状態によって評価を設定
    $max_review = 0;
    for($i=0;$i<count($_time_list);$i++){
      $review = $this->schedule_review($user_id, $_time_list[$i])["review"];
      if($review > $max_review) $max_review = $review;
      $_time_list[$i]["review"] = $review;
    }
    //優先度の最も高いものから返却する
    $ret = [];
    foreach($_time_list as $_item){
      $is_add = false;
      if($max_review>0){
        if($_item["review"]===$max_review){
          $is_add = true;
        }
      }
      else {
        if($_item["status"]==="free"){
          $is_add = true;
        }
      }
      if($is_add==true){
        //追加
        if(!isset($ret[$_item["remark"]])){
          $ret[$_item["remark"]] = [];
        }
        $ret[$_item["remark"]][]=$_item;
      }
    }
    return $ret;
  }
  private function schedule_review($user_id, $target){
    $ret = ['calendar'=>null, 'same_place'=>'', 'review'=>0];
    $prev = null;
    $next = null;

    if($target["status"]!=="free") return $ret;

    //上に隣接する授業設定を取得
    $prev_calendars = UserCalendar::where('user_id', $user_id)
                      ->findStatuses(['fix', 'confirm'])
                      ->where('start_time', $target["end_time"])
                      ->get();
    foreach($prev_calendars as $calendar){
      foreach($this->get_tags('lesson_place') as $tag){
        //体験希望所在地のフロアと同じ場合隣接とみなす
        if($calendar->is_same_place($tag->tag_value)){
          $prev = $calendar;
        }
        if(isset($prev)) break;
      }
      if(isset($prev)) break;
    }

    //近い日にちであるほど優先度はあがる
    $d = $this->day_diff($target["start_time"]);
    //今週= 100、来週=95、再来週=90
    $d = 100 - intval($d/7)*5;
    $ret['review']+=$d;

    //下に隣接する授業設定を取得
    $next_calendars = UserCalendar::where('user_id', $user_id)
                      ->findStatuses(['fix', 'confirm'])
                      ->where('end_time', $target["start_time"])
                      ->get();
    foreach($next_calendars as $calendar){
      $same_place = "";
      foreach($this->get_tags('lesson_place') as $tag){
        if($calendar->is_same_place($tag->tag_value)){
          $next  = $calendar;
        }
        if(isset($next)) break;
      }
      if(isset($next)) break;
    }

    $is_adjacent = false;
    //隣接：評価値+2、移動なし：評価値+1、
    if( (isset($prev) && isset($next)) ||
         (isset($prev) && !isset($next) && count($next_calendars)<1)){
       //１．上下隣接、２．上に隣接し下がない
       $ret['calendar'] = $prev;
       $ret['same_place'] = $prev->place_floor_id;
       $ret['review']+=3;
       $is_adjacent = true;
    }
    else if(!isset($prev) && isset($next) && count($prev_calendars)<1){
      //３．下に隣接し上がない
      $ret['calendar'] = $next;
      $ret['review']+=3;
      $ret['same_place'] = $next->place_floor_id;
      $is_adjacent = true;
    }
    else {
      //隣接しない
      $d = date('Y-m-d', strtotime($target["start_time"]));
      //同日のスケジュール
      $calendars = UserCalendar::where('user_id', $user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->findStatuses(['fix', 'confirm'])
                        ->get();

      if(count($calendars) < 1){
        //授業なし
        $ret['review']+=1;
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
          $ret['review']+=2;
        }
      }
    }

    //echo $target["start_time"].":".$ret['review']."/(".isset($prev)." | ".isset($next).")".$target["conflict_calendar"]["id"]."<br>";
    if($is_adjacent===true){
      $d = date('Y-m-d', strtotime($target["start_time"]));
      //同日のスケジュール
      $min_start_time = UserCalendar::where('user_id', $user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->where('status', 'fix')
                        ->min('start_time');
      $max_end_time = UserCalendar::where('user_id', $user_id)
                        ->rangeDate($d." 00:00:00",  $d." 23:59:59")
                        ->where('status', 'fix')
                        ->max('end_time');
      $is_inner = true;
      if(strtotime($target["start_time"]) < strtotime($min_start_time)){
        //echo "・上端更新：".$target["start_time"] ."<". $min_start_time."<br>";
        $is_inner = false;
      }
      if(strtotime($target["end_time"]) > strtotime($max_end_time)){
        //echo "・下端更新：".$target["end_time"] ."<". $max_end_time."<br>";
        $is_inner = false;
      }
      if($is_inner==true){
        //隣接し、かつ滞在時間拡大しない
        $ret['review']+=1;
      }
    }
    return $ret;
  }

  //体験希望スケジュールと、講師の勤務可能スケジュール・現在のスケジュール設定
  public function get_match_schedule($teacher){
    //１．この体験対象の生徒の希望スケジュールと希望授業時間を取得
    if(empty($this->student_schedule)){
      //兄弟登録された場合は一人目と同一のため、一人目のスケジュールを利用する
      $student = $this->student;
      $this->student_schedule = $student->user->get_lesson_times(10);
    }
    if($this->couser_minutes==0){
      if(!isset($student)) $student = $this->student;
      $this->course_minutes = intval($this->get_tag('course_minutes')['tag_value']);
    }

    //２．講師の勤務可能スケジュール、通常授業スケジュールを取得
    $teacher_enable_schedule = $teacher->user->get_lesson_times(10);
    $detail = [];
    $count = [];
    $student_schedule = [];
    $all_count = 0;
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
              $all_count+=$_slot;
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
          $all_count+=$_slot;
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
    return ["all_count" => $all_count, "detail" => $detail, "result" => $student_schedule];
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
        //echo "・上端更新：".$target["start_time"] ."<". $min_start_time."<br>";
        $is_inner = false;
      }
      if(strtotime("2000-01-01 ".$to_time_slot) > strtotime("2000-01-01 ".$max_to_time_slot)){
        //echo "・下端更新：".$target["end_time"] ."<". $max_end_time."<br>";
        $is_inner = false;
      }
      if($is_inner==true){
        //隣接し、かつ滞在時間拡大しない
        $ret['review']+=1;
      }
    }

    return $ret;
  }
  public function hope_to_join_ask($create_user_id, $access_key){
    //この体験に関してはいったん完了ステータス
    //保護者にアクセスキーを設定
    \Log::warning("hope_to_join_ask");

    $this->parent->user->update(['access_key' => $access_key]);
    //すでにある場合は一度削除
    Ask::where('target_model', 'trials')->where('target_model_id', $this->id)
        ->where('status', 'new')->where('type', 'hope_to_join')->delete();

    $ask = Ask::add([
      "type" => "hope_to_join",
      "end_date" => date("Y-m-d", strtotime("30 day")),
      "body" => "",
      "target_model" => "trials",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $this->parent->user_id,
      "charge_user_id" => 1,
    ]);
    //ステータス：入会希望連絡済み
    LessonRequest::where('id', $this->id)->update(['status' => 'entry_contact']);
    return $ask;
  }
  public function hope_to_join($is_commit=false, $form){
    \Log::warning("LessonRequest::hope_to_join start");
    if($is_commit==false){
      //ステータス：入会希望なし
      $update_data = [
        'status' => 'entry_cancel'
      ];
    }
    else {
      //ステータス：入会希望あり
      $update_data = [
        'status' => 'entry_hope',
        'schedule_start_hope_date' => $form['schedule_start_hope_date']
      ];

      //通塾可能曜日・時間帯タグ
      $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'english_talk_lesson', 'entry_milestone']; //生徒のuser_tagと共通
      $lesson_weeks = config('attribute.lesson_week');
      foreach($lesson_weeks as $lesson_week=>$name){
        $tag_names[] = 'lesson_'.$lesson_week.'_time';
      }
      foreach($tag_names as $tag_name){
        if(isset($form[$tag_name]) && count($form[$tag_name])>0){
          LessonRequestTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
        }
        else {
          LessonRequestTag::clearTags($this->id, $tag_name);
        }
      }
      $tag_names = ['piano_level', 'english_teacher', 'lesson_week_count', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes', 'course_type', 'entry_milestone_word'];
      foreach($tag_names as $tag_name){
        if(empty($form[$tag_name])) $form[$tag_name] = '';
        LessonRequestTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
      //科目タグ
      $charge_subject_level_items = GeneralAttribute::get_items('charge_subject_level_item');
      foreach($charge_subject_level_items as $charge_subject_level_item){
        $tag_names[] = $charge_subject_level_item['attribute_value'];
      }
      foreach($tag_names as $tag_name){
        if(empty($form[$tag_name])) $form[$tag_name] = '';
        LessonRequestTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
      $this->student->profile_update($form);
      $this->write_comment('entry');
    }
    LessonRequest::where('id', $this->id)->update($update_data);
    return true;
  }

  public function agreement_ask($create_user_id, $access_key){
    //この体験に関してはいったん完了ステータス
    //保護者にアクセスキーを設定

    $this->parent->user->update(['access_key' => $access_key]);
    LessonRequest::where('id', $this->id)->update(['status' => 'entry_guidanced']);

    Ask::where('target_model', 'trials')->where('target_model_id', $this->id)
        ->where('status', 'new')->where('type', 'agreement')->delete();

    $ask = Ask::add([
      "type" => "agreement",
      "end_date" => date("Y-m-d", strtotime("30 day")),
      "body" => "",
      "target_model" => "trials",
      "target_model_id" => $this->id,
      "create_user_id" => $create_user_id,
      "target_user_id" => $this->parent->user_id,
      "charge_user_id" => 1,
    ]);
    return $ask;
  }
  public function agreement($is_commit=false){
    if($is_commit==false){
    }
    else {
      $this->student->regular();
    }
    return true;
  }
  function day_diff($date1, $date2='now') {
    $timestamp1 = strtotime($date1);
    $timestamp2 = strtotime($date2);
    $seconddiff = abs($timestamp2 - $timestamp1);
    $daydiff = $seconddiff / (60 * 60 * 24);
    return $daydiff;
  }
  public function write_comment($type){
    $remark = $this->remark_full();

    $type_title = [
      "trial" => "体験申し込み時のご要望",
      "entry" => "入会希望時のご要望",
      "season_lesson" => "季節講習申し込み時のご要望",
    ];
    if(!empty($remark)){
      $comment = Comment::where('target_user_id', $this->student->user_id)
                        ->where('type', $type)->first();
      if(isset($comment)){
        $comment->update(['body' => $remark]);
      }
      else {
        Comment::create([
          'title' => $type_title[$type],
          'body' => $remark,
          'type' => $type,
          'create_user_id' => $this->create_user_id,
          'target_user_id' => $this->student->user_id,
          'publiced_at' => date('Y-m-d'),
          'importance' => 10,
        ]);
      }
    }
  }
  public function get_hope_date($day, $from_time_slot='', $to_time_slot=''){
    $d = LessonRequestDate::where('lesson_request_id', $this->id);
    $d = $d->where('day', $day);
    if($d!=null && !empty($from_time_slot)){
      $d = $d->where('from_time_slot', $from_time_slot);
    }
    if($d!=null && !empty($to_time_slot)){
      $d = $d->where('to_time_slot', $to_time_slot);
    }
    $d = $d->first();
    return $d;
  }
  public function charge_subject_attributes(){
    $attributes = GeneralAttribute::where('attribute_key', 'charge_subject')->get();
    return $attributes;
  }
}
