<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\StudentParent;
use App\Models\UserCalendarMember;
use App\Models\Lecture;
use App\Models\PlaceFloor;
use App\Models\Trial;
use App\User;
use DB;
use App\Models\Traits\Common;

class UserCalendar extends Model
{
  use Common;
  protected $pagenation_line = 20;
  protected $table = 'lms.user_calendars';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'start_time' => 'required',
      'end_time' => 'required'
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function place_floor(){
    return $this->belongsTo('App\Models\PlaceFloor', 'place_floor_id');
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMember', 'calendar_id');
  }
  public function tags(){
    return $this->hasMany('App\Models\UserCalendarTag', 'calendar_id');
  }
  public function setting(){
    return $this->belongsTo('App\Models\UserCalendarSetting', 'user_calendar_setting_id');
  }
  public function exchanged_calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'exchanged_calendar_id');
  }
  public function trial(){
    return $this->belongsTo('App\Models\Trial');
  }
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
    if($from_date == $to_date){
      $query = $query->where(DB::raw('cast(start_time as date)'), $from_date);
    }
    else {
      if(!empty($from_date)){
        $query = $query->where($field, '>=', $from_date);
      }
      if(!empty($to_date)){
        $query = $query->where($field, '<', $to_date);
      }
    }
    return $query;
  }
  public function scopeSearchDate($query, $from_date, $to_date)
  {
    $where_raw = <<<EOT
      ((user_calendars.start_time >= '$from_date'
       AND user_calendars.start_time <= '$to_date'
      )
      OR (user_calendars.end_time >= '$from_date'
        AND user_calendars.end_time <= '$to_date'
      ))
EOT;
    return $query->whereRaw($where_raw,[$from_date, $to_date]);

  }
  public function scopeSearchWord($query, $word)
  {
    $search_words = explode(' ', $word);
    $where_raw = <<<EOT
      user_calendars.remark like ?
      OR user_calendars.id in (
        select um.calendar_id from user_calendar_members um
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
      }
    });
    return $query;
  }
  public function get_search_word_where_string($word){
  }
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }
  public function scopeFindTeachingType($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'teaching_type', $vals, $is_not);
  }
  public function scopeFindWorks($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'work', $vals, $is_not);
  }
  public function scopeFindPlaces($query, $vals, $is_not=false)
  {
    $place_floors = PlaceFloor::whereIn('place_id', $vals)->get();
    $ids = [];
    foreach($place_floors as $place_floor){
      $ids[] = $place_floor->id;
    }
    return $this->scopeFieldWhereIn($query, 'place_floor_id', $ids, $is_not);
  }
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
    if(count($vals) > 0){
      if($is_not===true){
        $query = $query->whereNotIn($field, $vals);
      }
      else {
        $query = $query->whereIn($field, $vals);
      }
    }
    return $query;
  }
  public function scopeFindTrialStudent($query, $trial_id)
  {
    $where_raw = <<<EOT
      lms.user_calendars.id in (
        select calendar_id from
        lms.user_calendar_members um
        inner join common.students s on s.user_id = um.user_id
        inner join lms.trial_students ts on s.id = ts.student_id
        where ts.trial_id=?)
EOT;

    return $query->whereRaw($where_raw,[$trial_id]);
  }
  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      user_calendars.id in (select calendar_id from user_calendar_members where user_id=?)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }
  public function scopeFindExchangeTarget($query, $user_id=0, $lesson=0)
  {
    $from = date("Y-m-01 00:00:00", strtotime("-1 month "));
    $to = date("Y-m-01", strtotime("+2 month ".$from));
    //先月～今月末の対象生徒が、休みかつ、規定回数以上ではない
    //かつ、振替が未登録(cancelは除く）
    $param = [];
    $where_raw = <<<EOT
      user_calendars.id in (
        select um.calendar_id from user_calendar_members um
          inner join common.students s on s.user_id = um.user_id
        where
          um.status = 'rest'
          and um.exchange_limit_date >= current_date

      and user_calendars.course_minutes > (
        select ifnull(sum(ec.course_minutes),0) from user_calendars ec where ec.exchanged_calendar_id = user_calendars.id
      )
EOT;
    if($user_id > 0){
      $param[] = $user_id;
      $where_raw .= <<<EOT
       and um.user_id = ?
EOT;
    }
    $where_raw .= <<<EOT
        )
        and user_calendars.id in (
          select calendar_id from user_calendar_tags where
            tag_value = 'single'
            and tag_key = 'course_type'
          )
EOT;
    if($lesson > 0){
      $param[] = $lesson;
      $where_raw .= <<<EOT
          and user_calendars.id in (
            select calendar_id from user_calendar_tags where
              tag_value = ?
              and tag_key = 'lesson'
            )
EOT;
    }
    $query = $query->whereRaw($where_raw,$param);
    $query = $this->scopeSearchDate($query, $from, $to);
    return $query;
  }
  public function get_access_member($user_id){
    $ret = [];
    if($user_id < 2){
      //user_id 指定なし＝root扱い
      $user = User::where('id', 1)->first();
      $user = $user->details("managers");
    }
    else {
      $user = User::where('id', $user_id)->first();
      $user = $user->details("teachers");
      if(empty($user->role)){
        $user = $user->details();
      }
    }

    if(!isset($user)) {
      return $ret;
    }
    if($user->role=='parent'){
      //保護者の場合、自分の子供のみアクセス可能
      foreach ($user->relation() as $relation){
        $member = $this->get_member($relation->student->user_id);
        if(!empty($member)){
          $ret[] = $member;
        }
      }
    }
    else if($user->role=='manager'){
      //事務＝全員
      return $this->members;
    }
    else if($user->role=='teacher'){
      //講師＝全員
      return $this->members;
    }
    else {
      //生徒＝自分のみ
      $member = $this->get_member($user_id);
      if(!empty($member)){
        $ret[] = $member;
      }
    }
    return $ret;
  }
  public function is_access($user_id){
    $parent = StudentParent::where('user_id', $user_id)->first();
    if(isset($parent)){
      //保護者の場合
      foreach ($parent->relation() as $relation){
        if(!empty($this->get_member($relation->student->user_id))){
          return true;
        }
      }
      return false;
    }
    else {
      if(!empty($this->get_member($user_id))){
        return true;
      }
      return false;
    }
  }
  //振替対象の場合:true
  public function is_exchange_target(){

    $d = intval(strtotime('now')) - intval(strtotime($this->start_time));
    $c = 0;
    if($d < 0) return false;
    //過去の予定であること
    $c++;
    if($this->is_single()==false) return false;
    //マンツーであること
    $c++;
    if($this->is_rest_status($this->status)==false) return false;
    //ステータス＝休み
    $students = $this->get_students();
    $c++;
    if(count($students)!=1) return false;
    $c++;
    if($students[0]->is_exchange_target()!=true) return false;
    $c++;
    return true;
  }
  public function is_prev_rest_contact(){
    //休みの事前連絡が可能かどうか
    $base_day = date('Y-m-d 21:00:00', strtotime('-1 day '.$this->start_time));
    if(strtotime($base_day) < strtotime('now')){
      //前日21:00過ぎたら、事前連絡にならない
      return false;
    }
    return true;
  }
  public function has_tag($key, $val=""){
    $tags = $this->tags;
    foreach($tags as $tag){
      if(empty($val) && $tag->tag_key==$key) return true;
      if($tag->tag_key==$key && $tag->tag_value==$val) return true;
    }
    return false;
  }
  public function get_tag($key){
    $item = $this->tags->where('tag_key', $key)->first();
    if(isset($item)){
      return $item;
    }
    return null;
  }
  public function get_tags($key){
    $item = $this->tags->where('tag_key', $key);
    if(isset($item)){
      return $item;
    }
    return null;
  }
  public function get_tag_name($tag_key){
    $tag =  $this->get_tag($tag_key);
    if(isset($tag)){
      return $tag->name();
    }
    return "";
  }
  public function get_tag_value($tag_key){
    $tag =  $this->get_tag($tag_key);
    if(isset($tag)){
      return $tag->tag_value;
    }
    return "";
  }
  public function get_attribute_name($key, $val){
    $item = GeneralAttribute::findKeyValue($key,$val)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  public function lesson($is_value=false){
    $en = [1=>'School', 2=>'English Talk', 3=>'Piano', 4=>'Kids Lesson'];
    $val = $this->get_attribute('lesson', $is_value);
    if(is_numeric($val) && $is_value==false) return $en[intval($val)];
    return $val;
  }
  public function course($is_value=false){
    return $this->get_attribute('course_type', $is_value);
  }
  public function course_minutes($is_value=false){
    //return $this->get_attribute('course_minutes', $is_value);
    if($is_value==true) return $this->course_minutes;
    return $this->get_attribute_name('course_minutes', $this->course_minutes);
  }
  public function get_attribute($key, $is_value=false){
    if($is_value==true || app()->getLocale()=='en'){
      $t = $this->get_tag($key);
      if(isset($t)){
        return $t->tag_value;
      }
      return 0;
    }
    return $this->get_tag_name($key);
  }
  public function work(){
    if(!isset($this->work)) return "";
    return $this->get_attribute_name('work', $this->work);
  }
  public function schedule_type_code(){
    switch($this->work){
      case 1:
      case 2:
      case 3:
        return 'interview';
      case 4:
        return 'examination_director';
      case 5:
        return 'training';
      case 6:
      case 7:
      case 8:
        return 'school_lesson';
      case 9:
        return 'office_work';
      case 10:
        return 'season_school_lesson';
    }
    return "";
  }
  public function schedule_type_name(){
    $code = $this->schedule_type_code();
    return __('labels.'.$code);
  }
  public function get_teaching_type(){
    if($this->work==10) return 'season';
    if($this->work==5) return 'training';

    if($this->is_teaching()){
      if($this->trial_id > 0){
        return 'trial';
      }
      if(intval($this->user_calendar_setting_id) > 0){
        return 'regular';
      }
      if($this->exchanged_calendar_id > 0){
        return 'exchange';
      }
      return 'add';
    }
    return "";
  }
  public function teaching_type_name(){
    $ret = $this->get_attribute_name('teaching_type', $this->teaching_type);
    if(empty($ret)){
      $type = $this->get_teaching_type();
      UserCalendar::where('id', $this->id)->update(['teaching_type' => $type]);
      $ret = $this->get_attribute_name('teaching_type', $type);
    }
    return $ret;
  }
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.calendar_status')[$this->status])){
      $status_name = config('attribute.calendar_status')[$this->status];
    }
    switch($this->status){
      case "fix":
        if($this->work==9) return "勤務予定";
      case "absence":
        if($this->work==9) return "欠勤";
      case "presence":
      if($this->work==9) return "出勤";
    }
    return $status_name;
  }
  public function subject(){
    $tags =  $this->get_tags('charge_subject');
    $ret = [];
    if(isset($tags)){
      foreach($tags as $index => $tag){
        $ret[] = $tag->name();
      }
    }
    $tags =  $this->get_tags('english_talk_lesson');
    if(isset($tags)){
      foreach($tags as $index => $tag){
        $ret[] = $tag->name();
      }
    }
    $tags =  $this->get_tags('piano_lesson');
    if(isset($tags)){
      foreach($tags as $index => $tag){
        $ret[] = $tag->name();
      }
    }
    $tags =  $this->get_tags('kids_lesson');
    if(isset($tags)){
      foreach($tags as $index => $tag){
        $ret[] = $tag->name();
      }
    }
    if(count($ret)==0){
      $tags =  $this->get_tags('subject_expr');
      foreach($tags as $index => $tag){
        $ret[] = $tag->tag_value;
      }
    }
    return $ret;
  }
  public function is_management(){
    if(empty($this->work)) return false;
    if(intval($this->work)==9) return true;
    if(intval($this->work)<6) return true;
    return false;
  }
  public function is_group(){
    $tag =  $this->get_tag('course_type');
    if(isset($tag) && $tag->tag_value=="group") return true;
    /*
    $students = $this->get_students();
    //course_typeに限らず、生徒が複数いるかどうか
    if(count($students) > 1) return true;
    */
    return false;
  }
  public function is_trial(){
    if($this->trial_id > 0) return true;
    if($this->is_group()==true){
      foreach($this->members as $member){
        if($member->status=='trial') return true;
      }
    }
    return false;
  }
  public function is_single(){
    $tag =  $this->get_tag('course_type');
    if(isset($tag) && $tag->tag_value=="single") return true;
    return false;
  }
  public function timezone(){
    $start_hour_minute = date('H:i',  strtotime($this->start_time));
    $end_hour_minute = date('H:i',  strtotime($this->end_time));
    return $start_hour_minute.'～'.$end_hour_minute;
  }
  public function dateweek(){
    return $this->dateweek_format($this->start_time);
  }
  public function dateweek_format($date){
    $format = "n月j日";
    $weeks = config('week');
    if(app()->getLocale()=='en'){
      $format = "n/j";
      $weeks = config('week_en');
    }
    $d = date($format,  strtotime($date));
    $d .= '('.$weeks[date('w',  strtotime($this->start_time))].')';
    return $d;
  }
  public function get_member($user_id){
    return $this->members->where('user_id', $user_id)->first();
  }
  public function details($user_id=0){

    $item = $this;
    $item['teaching_name'] = $this->teaching_type_name();
    $item['status_name'] = $this->status_name();
    $item['schedule_type_code'] = $this->schedule_type_code();
    $item['schedule_type_name'] = $this->schedule_type_name();
    $item['place_floor_name'] = "";
    if(isset($this->place_floor)){
      $item['place_floor_name'] = $this->place_floor->name();
    }
    $item['work_name'] = $this->work();
    $item['teaching_name'] = $this->teaching_type_name();

    $item['date'] = date('Y/m/d',  strtotime($this->start_time));
    $item['dateweek'] = $this->dateweek();

    $diff = strtotime($this->start_time) - strtotime('now');
    //過ぎた予定かどうか
    $item['is_passed'] = true;
    if($diff > 0) {
      $item['is_passed'] = false;
    }

    $item['start_hour_minute'] = date('H:i',  strtotime($this->start_time));
    $item['end_hour_minute'] = date('H:i',  strtotime($this->end_time));
    $item['course_minutes'] = $this->course_minutes;
    $item['timezone'] = $this->timezone();
    $item['datetime'] = $this->dateweek().' '.$item['start_hour_minute'].'～'.$item['end_hour_minute'];
    if($this->lecture_id > 0){
      $lecture = $this->lecture->details();
    }
    $item['lesson'] = $this->lesson();
    $item['course'] = $this->course();
    $item['subject'] = $this->subject();
    $teacher_name = "";
    $student_name = "";
    $manager_name = "";
    $teachers = [];
    $students = [];
    $managers = [];
    $item['managers'] = [];
    foreach($this->members as $member){
      $_member = $member->user->details('teachers');
      if($_member->role === 'teacher'){
        $teacher_name.=$_member['name'].',';
        $teachers[] = $member;
      }
      $_member = $member->user->details('managers');
      if($_member->role === 'manager' || $_member->role === 'staff'){
        $manager_name.=$_member['name'].',';
        $managers[] = $member;
      }
    }
    //グループレッスンの場合など、ユーザーがアクセス可能な生徒を表示する
    foreach($this->get_access_member($user_id) as $member){
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
    $item['manager_name'] = trim($manager_name,',');
    $item['user_name'] = $this->user->details()->name();
    $item['is_exchange'] = false;
    $item['exchange_remaining_time'] = $this->get_exchange_remaining_time();
    if(is_numeric($item['exchanged_calendar_id']) && $item['exchanged_calendar_id']>0){
      $item['is_exchange'] = true;
    }
    //fullcalendar向けのパラメータ
    $item['start'] = $this->start_time;
    $item['end'] = $this->end_time;
    $item['total_status'] = $this->status;

    return $item;
  }
  static public function get_holiday($day, $is_public=true, $is_private=true){
    $day = date('Y-m-d', strtotime($day));
    $holiday = Holiday::where('date', $day)
            ->first();
    if(isset($holiday)){
      return $holiday;
    }
    return null;
  }
  public function is_holiday($date=""){
    if(empty($date)) $date = $this->start_time;
    $holiday = (new UserCalendar())->get_holiday($date);
    if($holiday!=null) return true;
    return false;
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){

    $ret = [];
    $trial_id = 0;
    $user_calendar_setting_id = 0;
    if(isset($form['user_calendar_setting_id'])){
      $user_calendar_setting_id = $form['user_calendar_setting_id'];
    }
    if(isset($form['trial_id'])) $trial_id = $form['trial_id'];

    //TODO 重複登録、競合登録の防止が必要
    /*
    $calendar = UserCalendar::searchDate($form['start_time'], $form['end_time'])
      ->findStatuses(['rest', 'cancel', 'lecture_cancel'], true)
      ->where('user_id', $form['teacher_user_id'])->first();

    if(isset($calendar)){
      return $this->error_response("同じ時間の予定が存在します", "", $form);
    }
    */

    $course_minutes = intval(strtotime($form['end_time']) - strtotime($form['start_time']))/60;

    $calendar = UserCalendar::create([
      'start_time' => $form['start_time'],
      'end_time' => $form['end_time'],
      'lecture_id' => 0,
      'course_minutes' => $course_minutes,
      'trial_id' => $trial_id,
      'user_calendar_setting_id' => $user_calendar_setting_id,
      'exchanged_calendar_id' => $form['exchanged_calendar_id'],
      'place_floor_id' => $form['place_floor_id'],
      'work' => '',
      'remark' => '',
      'user_id' => $form['teacher_user_id'],
      'create_user_id' => $form['create_user_id'],
      'status' => 'new'
    ]);

    $teaching_type = $calendar->get_teaching_type();
    $calendar->update(['teaching_type' => $teaching_type]);

    $calendar->memberAdd($form['teacher_user_id'], $form['create_user_id'], 'new', false);
    //新規登録時に変更メールを送らない
    unset($form['send_mail']);
    $calendar = $calendar->change($form);

    return $calendar->api_response(200, "", "", $calendar);
  }
  //本モデルはdeleteではなくdisposeを使う
  public function dispose(){
    //事務システム側を先に削除
    $this->office_system_api("DELETE");
    UserCalendarMember::where('calendar_id', $this->id)->delete();
    UserCalendarTag::where('calendar_id', $this->id)->delete();
    $this->delete();
  }
  //本モデルはupdateではなくchangeを使う
  public function change($form){
    $old_item = $this->replicate();

    $update_fields = [
      'status',
      'start_time',
      'end_time',
      'remark',
      'place_floor_id',
      'work'
    ];

    $status = $this->status;
    $is_status_update = true;

    //TODO Workの補間どうにかしたい
    if(isset($form['course_type']) && !isset($form['work'])){
      $work_data = ["single" => 6, "group"=>7, "family"=>8];
      $form['work'] = $work_data[$form["course_type"]];
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

    $data = [
      'status' => $status,
      'lecture_id' => $lecture_id,
    ];

    foreach($update_fields as $field){
      if($field=='status') continue;
      if(!isset($form[$field])) continue;
      $data[$field] = $form[$field];
    }
    if(isset($data['start_time']) && isset($data['end_time'])){
      //course_minutesは、start_time・end_timeから補完
      $data['course_minutes'] = intval(strtotime($data['end_time']) - strtotime($data['start_time']))/60;
    }
/*
    foreach($data as $field=>$val){
      \Log::warning($field."=".$val);
    }
*/
    if(isset($data['status_name']))  unset($data['status_name']);
    $this->update($data);
    if($this->trial_id > 0 && isset($form['status'])){
      //体験授業予定の場合、体験授業のステータスも更新する
      Trial::where('id', $this->trial_id)->first()->update(['status' => $status]);
    }
    $tag_names = ['matching_decide_word', 'course_type', 'lesson'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    $tag_names = ['matching_decide', 'charge_subject', 'kids_lesson', 'english_talk_lesson', 'piano_lesson'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    //事務システムも更新
    $this->office_system_api("PUT");

    if(isset($form['send_mail'])){
      $is_teacher_mail = false;
      $is_student_mail = false;
      if($form['send_mail']=='both'){
        $is_teacher_mail = true;
        $is_student_mail = true;
      }
      else if($form['send_mail']=='teacher'){
        $is_teacher_mail = true;
      }

      if($is_teacher_mail==true){
        $this->teacher_mail('予定変更につきましてご確認ください', ['old_item' => $old_item], 'text', 'calendar_update');
      }
      if($is_student_mail==true){
        $this->student_mail('予定変更につきましてご確認ください', ['old_item' => $old_item], 'text', 'calendar_update');
      }
    }
    return $this;
  }
  public function memberAdd($user_id, $create_user_id, $status='new', $is_api=true){
    if(empty($user_id) || $user_id < 1) return null;
    $member = UserCalendarMember::where('calendar_id' , $this->id)
      ->where('user_id', $user_id)->first();

    if(isset($memeber)){
      $member = $memeber->update(['status', $status]);
    }
    else {
      $member = UserCalendarMember::create([
          'calendar_id' => $this->id,
          'user_id' => $user_id,
          'status' => $status,
          'create_user_id' => $create_user_id,
      ]);
      if($is_api===true){
        //事務システムにも登録
        $member->office_system_api("POST");
      }
    }
    return $member;
  }
  public function checked($check_date){
    $this->update(['checked_at' => $check_date]);
    return false;
  }
  public function is_cancel_status(){
    if($this->status==="lecture_cancel" || $this->status==="cancel" || $this->status==="rest"){
      return true;
    }
    return false;
  }
  public function is_last_status(){
    if($this->status==="lecture_cancel" || $this->status==="cancel" || $this->status==="rest" || $this->status==="absence" || $this->status==="presence"){
      return true;
    }
    return false;
  }
  public function is_checked(){
    if(!empty($this->checked_at)){
      return true;
    }
    return false;
  }
  public function is_member($user_id){
    foreach($this->members as $member){
      if($member->user_id === $user_id){
        return true;
      }
    }
    return false;
  }
  public function is_members($members){
    foreach($members as $member){
      if($this->is_member($member->user_id)===false) return false;
    }
    return true;
  }
  public function is_teaching(){
    switch(intval($this->work)){
      case 6:
      case 7:
      case 8:
        return true;
    }
    return false;
  }
  public function is_conflict($start_time, $end_time, $place_id=0, $place_floor_id=0){
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $calendar_starttime = strtotime($this->start_time);
    $calendar_endtime = strtotime($this->end_time);
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
  public function is_time_connect($start_time, $end_time){
    //開始終了が一致した場合、場所をチェック
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $calendar_starttime = strtotime($this->start_time);
    $calendar_endtime = strtotime($this->end_time);

    if($end == $calendar_starttime || $start == $calendar_endtime) return true;
    return false;
  }
  public function is_same_place($place_id=0, $place_floor_id=0){
    //場所のチェック　フロアから所在地を出して、所在地単位でチェックする
    //echo "is_same_place_check:";
    if(!empty($place_id)){
      if(isset($this->place_floor) && $this->place_floor->place_id==$place_id){
        //echo "true<br>";
        return true;
      }
    }
    else if(!empty($place_floor_id)){
      if(isset($this->place_floor) && $this->place_floor->id==$place_floor_id){
        //echo "true<br>";
        return true;
      }
    }
    //echo "false<br>";
    return false;
  }
  public function office_system_api($method){
    $res = null;
    foreach($this->members as $member){
      $res = $member->office_system_api($method);
      if(!isset($res)) continue;
      if($res=="null" || !isset($res["status"])) break;
      if($res["status"]!=0) break;
    }
    return $res;
  }
  public function teacher_mail($title, $param, $type, $template){
    $param['send_to'] = 'teacher';
    $param['item'] = $this->details(1);
    foreach($this->members as $member){
      $u = $member->user->details('teachers');
      if($u->role != "teacher") continue;
      $param['user_name'] = $u->name();
      $member->user->send_mail($title, $param, $type, $template, $member->user->get_locale());
    }
    return;
  }
  public function student_mail($title, $param, $type, $template, $is_rest_send=false){
    $param['send_to'] = 'student';
    $param['item'] = $this->details(1);
    foreach($this->members as $member){
      $u = $member->user->details('students');
      if($u->role != "student") continue;
      //休み予定の場合送信しない
      if($is_rest_send==false && $member->status=='rest') continue;
      $param['user_name'] = $u->name();
      $member->user->send_mail($title, $param, $type, $template, $member->user->get_locale());
    }
    return;
  }
  public function get_students(){
    $students = [];
    //foreach($this->get_access_member($user_id) as $member){
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role === 'student'){
        $students[] = $member;
      }
    }
    return $students;
  }
  public function get_teachers(){
    $teachers = [];
    //foreach($this->get_access_member($user_id) as $member){
    foreach($this->members as $member){
      $_member = $member->user->details('teachers');
      if($_member->role === 'teacher'){
        $teachers[] = $member;
      }
    }
    return $teachers;
  }
  public function is_school(){
    return $this->has_tag('lesson', 1);
  }
  public function is_english_talk_lesson(){
    return $this->has_tag('lesson', 2);
  }
  public function is_piano_lesson(){
    return $this->has_tag('lesson', 3);
  }
  public function is_kids_lesson(){
    return $this->has_tag('lesson', 4);
  }
  public function status_to_confirm($remark, $login_user_id){
    if($this->status!='new') return false;
    $status = 'confirm';
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role != 'student') continue;
    }
    $this->update(['status' => $status]);

  }

  public function status_to_fix($remark, $login_user_id){
    if($this->status!='confirm' && $this->status!='cancel') return false;
    $is_update = true;
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role != 'student') continue;
      if($member->status!='fix' && $member->status!='cancel'){
        //fix or cancel以外があれば全体更新なし
        $is_update = false;
      }
    }
    if($is_update){
      //全員キャンセル or 一人以上出席
      $this->update(['status' => 'fix']);
    }
  }
  public function status_to_cancel($remark, $login_user_id){
    if($this->status!='confirm' && $this->status!='fix') return false;
    $status = 'cancel';
    $is_update = true;
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role != 'student') continue;
      if($member->status!='fix' && $member->status!='cancel'){
        //fix or cancel以外があれば全体更新なし
        $is_update = false;
      }
      if($member->status=='fix'){
        //fix が1つ以上あれば fix
        $status='fix';
      }
    }
    if($is_update){
      //全員キャンセル or 一人以上出席
      $this->update(['status' => $status]);
    }
  }
  public function status_to_rest($remark, $login_user_id){
    if($this->status != 'fix') return false;
    $status = 'rest';
    $is_update = true;
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role != 'student') continue;
      if($member->status!='rest'){
        //fix or cancel以外があれば全体更新なし
        $is_update = false;
      }
    }
    if($is_update){
      //全員休んだらカレンダー休み
      $this->update(['status' => $status]);
    }
  }
  public function status_to_absence($remark, $login_user_id){
    //授業予定→出席、　出席（間違えた場合）→欠席
    if($this->status!='fix' && $this->status!="presence") return false;
    $status = 'absence';
    //カレンダー：欠席
    $this->update(['status' => $status]);
  }
  public function status_to_presence(){
    //授業予定→出席、　欠席（間違えた場合）→出席
    if($this->status!='fix' && $this->status!="absence") return false;
    $status = 'presence';
    //カレンダー：出席
    $this->update(['status' => $status]);
  }
  public function exist_rest_student(){
    //欠席 or 休み or　休講
    foreach($this->members as $member){
      $_member = $member->user->details('students');
      if($_member->role == 'student' && $this->is_rest_status($member->status)==true){
        return true;
      }
    }
    return false;
  }
  public function is_rest_status($status){
    if($status=="rest") return true;
    if($status=="lecture_cancel") return true;
    if($status=="absence") return true;
    return false;
  }
  //振替可能残り時間
  public function get_exchange_remaining_time(){
    $students = $this->get_students();
    if(count($students)<1) return 0;
    return $students[0]->get_exchange_remaining_time();
  }
  //振替期限
  public function exchange_limit_date(){
    $students = $this->get_students();
    return $this->dateweek_format($students[0]->exchange_limit_date);
  }
}
