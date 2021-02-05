<?php
namespace App\Models\Traits;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAttribute;
use App\Models\MailLog;
use App\Models\UserCalendar;
use App\Models\UserCalendarSetting;

trait Matching
{
  /**
   *　スコープ：ステータス
   */
   public function scopeFindStatuses($query, $vals, $is_not=false)
   {
     return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
   }
   public function scopeSearchDate($query, $day, $from_time_slot='', $to_time_slot='')
   {
     return $query->whereHas('request_dates', function($query) use ($day, $from_time_slot, $to_time_slot) {
         $query = $query->where(function($query)use($day, $from_time_slot, $to_time_slot){
             $query = $query->Where(function($query)use($day, $from_time_slot, $to_time_slot){
             $query->where('day', $day);
             if(!empty($from_time_slot)){
               //from_time_slot指定あり　指定時間が希望範囲にある申し込みを取得
               $query->where('from_time_slot', '>=' , $from_time_slot)->where('to_time_slot', '>', $from_time_slot);
               if(!empty($to_time_slot)){
                 //さらにto_time_slot指定がある場合は、指定時間帯が内包する申し込みを取得
                 $query->where('from_time_slot', '>=' , $to_time_slot)->where('to_time_slot', '>=', $to_time_slot);
               }
             }
           });
         });
     });
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
  private function get_time_list($lesson){
    $time_lists = [];
    foreach($this->request_dates->sortBy('sort_no') as $d){
      $time_lists[$d->day] = $this->_get_time_list($d->from_datetime, $d->to_datetime, $lesson);
    }
    return $time_lists;
  }
  private function _get_time_list($trial_start_time, $trial_end_time, $lesson){
    $_start = $trial_start_time;
    $time_list = [];
    if(empty($trial_start_time) || empty($trial_end_time)){
      return $time_list;
    }
    /*TODO デバッグのため一時的にコメントアウト 2021/1/15
    if(strtotime("now") > strtotime($trial_start_time)){
      return [];
    }
    */
    //体験授業は、30分、60分の2択
    $course_minutes = 30;
    if($lesson==1) $course_minutes = 60;
    //１０分ずらしで、授業時間分の範囲を配列に設定する
    $slot_unit_minutes = 10;
    if($this->type=='season_lesson') $slot_unit_minutes = 30;

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
      ];
      $_start = date("Y-m-d H:i:s", strtotime("+".$slot_unit_minutes." minute ".$_start));
    }
    return $time_list;
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
  public function get_hope_date($target_day, $from_time_slot='', $to_time_slot=''){
    $d = LessonRequestDate::where('lesson_request_id', $this->id);
    $d = $d->where('day', $target_day);
    if($d!=null && !empty($from_time_slot)){
      $d = $d->where('from_time_slot', $from_time_slot);
    }
    if($d!=null && !empty($to_time_slot)){
      $d = $d->where('to_time_slot', $to_time_slot);
    }
    $d = $d->first();
    return $d;
  }
  public function getScheduleStartHopeDateAttribute(){
    return $this->get_tag_value('schedule_start_hope_date');
  }
  public function charge_subject_attributes(){
    $attributes = GeneralAttribute::where('attribute_key', 'charge_subject')->get();
    return $attributes;
  }
  public function is_hope_exchange(){
    return $this->has_tag('regular_schedule_exchange', 'true');
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
          $teacher->enable_point = $enable_point;
          $teacher->disable_point = $disable_point;
          $teacher->subject_review = 'part';
          if($disable_point < 1) $teacher->subject_review = 'all';
          $teacher->enable_subject = $enable_subject;
          $teacher->disable_subject = $disable_subject;
          $teacher->match_schedule = $this->get_match_schedule($teacher->id);
          $teacher->matching_lessons = $this->create_matching_lessons($lesson, $teacher->id);
          $ret[] = $teacher;
      }
    }
    return $ret;
  }


  public function create_matching_lessons($lesson, $teacher_id, $target_day='', $place_id=0, $subject_code=""){
    foreach($this->get_time_list($lesson) as $day => $time_list){
      if(!empty($target_day) && $day!=$target_day) continue;
      $this->create_request_calendar($lesson, $time_list, $teacher_id, $day, $place_id, $subject_code);
    }
    return $this->request_calendar_review($teacher_id, $target_day);
  }
  public function get_matching_result($target_calendar, $teacher_calendars, $place_id=0){
    $conflict_calendar = null;
    $free_place_floor = "";
    $matching_result = "";
    $is_time_conflict = false;
    $is_place_conflict = false;

    foreach($teacher_calendars as $teacher_calendar){
      if($is_time_conflict===false){
        if($teacher_calendar->is_conflict($target_calendar->start_time, $target_calendar->end_time)){
          //時間が競合した場合
          $conflict_calendar = $teacher_calendar;
          $is_time_conflict = true;
          $matching_result = "time_conflict";
        }
      }
      if($is_time_conflict===false && $is_place_conflict===false){
        $_free_place = "";
        $__conflict = true;
        foreach($this->get_tags('lesson_place') as $tag){
          //place_id指定がある場合、指定以外は無視
          if($place_id > 0 && $tag->tag_value != $place_id) continue;
          if(!$teacher_calendar->is_conflict($target_calendar->start_time, $target_calendar->end_time, $tag->tag_value)){
            //場所の競合もなし=選択候補
            $__conflict = false;
            if($teacher_calendar->is_same_place($tag->tag_value)){
              $_free_place = $teacher_calendar->place_floor_id;
            }
          }
        }
        $is_place_conflict = $__conflict;
        if($__conflict===true){
          $conflict_calendar = $teacher_calendar;
          $matching_result = "place_conflict";
        }
        else {
          //空いている
          $free_place_floor = $_free_place;
        }
      }
    }
    return $matching_result;
    //return ['matching_result' => $matching_result, 'free_place_floor' => $free_place_floor, 'conflict_calendar' => $conflict_calendar];
  }
}
