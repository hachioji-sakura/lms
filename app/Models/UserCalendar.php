<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\StudentParent;
use App\Models\UserCalendarMember;
use App\Models\Lecture;
use App\Models\Trial;
use App\User;

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
  public function lecture(){
    return $this->belongsTo('App\Models\Lecture');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function members(){
    return $this->hasMany('App\Models\UserCalendarMember', 'calendar_id');
  }
  public function tags(){
    return $this->hasMany('App\Models\UserCalendarTag', 'calendar_id');
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
  public function scopeFindExchangeTarget($query)
  {
    $from = date("Y-m-01 00:00:00", strtotime("-1 month "));
    $to = date("Y-m-01", strtotime("+2 month ".$from));
    $query = $this->scopeSearchDate($query, $from, $to);
    $where_raw = <<<EOT
      user_calendars.id not in (select exchanged_calendar_id from user_calendars)
      and user_calendars.status = 'rest'
EOT;
    return $query->whereRaw($where_raw,[]);
  }
  public function get_access_member($user_id){
    $user = User::where('id', $user_id)->first();
    if(isset($user)) $user = $user->details();
    $ret = [];
    if($user->role=='parent'){
      //保護者の場合
      foreach ($user->relation() as $relation){
        $member = $this->get_member($relation->student->user_id);
        if(!empty($member)){
          $ret[] = $member;
        }
      }
    }
    else if($user->role=='manager'){
      return $this->members;
    }
    else {
      $member = $this->get_member($user_id);

      if(!empty($member)){
        $ret[] = $member;
        if($user->role=='teacher'){
          return $this->members;
        }
        else {
          return $member;
        }
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
  public function lesson(){
    $tag =  $this->get_tag('lesson');
    if(isset($tag)){
      return $tag->name();
    }
    return "";
  }
  public function course(){
    $tag =  $this->get_tag('course_type');
    if(isset($tag)){
      return $tag->name();
    }
    return "";
  }
  public function is_group(){
    $tag =  $this->get_tag('course_type');
    if($tag->tag_value!="single") return true;
    return false;
  }
  public function timezone(){
    $start_hour_minute = date('H:i',  strtotime($this->start_time));
    $end_hour_minute = date('H:i',  strtotime($this->end_time));
    return $start_hour_minute.'～'.$end_hour_minute;
  }
  public function get_member($user_id){
    return $this->members->where('user_id', $user_id)->first();
  }
  public function details($user_id=0){
    $item = $this;
    $item['status_name'] = $this->status_name();
    $item['place_name'] = $this->place();
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
      if($_member->role === 'manager'){
        $other_name.=$_member['name'].',';
        $managers[] = $member;
      }
    }
    if($user_id > 0){
      //グループレッスンの場合など、ユーザーがアクセス可能な生徒を表示する
      foreach($this->get_access_member($user_id) as $member){
        if(!isset($member->user)) continue;
        $_member = $member->user->details('students');
        if($_member->role === 'student'){
          $student_name.=$_member['name'].',';
          $students[] = $member;
        }
      }
    }
    unset($item['members']);
    unset($item['lecture']);
    $item['teachers'] = $teachers;
    $item['students'] = $students;
    $item['managers'] = $managers;
    $item['student_name'] = trim($student_name,',');
    $item['teacher_name'] = trim($teacher_name,',');
    $item['other_name'] = trim($other_name,',');
    $item['is_exchange'] = false;
    if(is_numeric($item['exchanged_calendar_id']) && $item['exchanged_calendar_id']>0){
      $item['is_exchange'] = true;
    }
    return $item;
  }
  //本モデルはcreateではなくaddを使う
  static protected function add($form){
    $ret = [];
    $trial_id = 0;
    $user_calendar_setting_id = 0;
    if(isset($form['user_calendar_setting_id'])){
      $user_calendar_setting_id = $form['user_calendar_setting_id'];
    }
    if(isset($form['trial_id'])){
      $trial_id = $form['trial_id'];
    }

    $calendar = UserCalendar::create([
      'start_time' => $form['start_time'],
      'end_time' => $form['end_time'],
      'lecture_id' => 0,
      'trial_id' => $trial_id,
      'user_calendar_setting_id' => $user_calendar_setting_id,
      'exchanged_calendar_id' => $form['exchanged_calendar_id'],
      'place' => '',
      'work' => '',
      'remark' => '',
      'user_id' => $form['teacher_user_id'],
      'create_user_id' => $form['create_user_id'],
      'status' => 'new'
    ]);
    $calendar->memberAdd($form['teacher_user_id'], $form['create_user_id'], 'new', false);
    $calendar = $calendar->change($form);
    return $calendar;
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

    $update_fields = [
      'status',
      'access_key',
      'start_time',
      'end_time',
      'remark',
      'place',
      'work'
    ];
    $status = $this->status;
    if(isset($form['status'])){
      $is_status_update = true;
      if($form['status']=='rest' || $form['status']=='fix' || $form['status']=='cancel'){
        //status=restは生徒全員が休みの場合
        //status=fixは生徒全員が予定確認した場合
        foreach($this->members as $member){
          if($member->user->details('students')->role == "student"){
            if($member->status != $form['status']){
              $is_status_update = false;
            }
          }
        }
      }
      if($is_status_update === true)  $status = $form['status'];
    }

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
    $this->update($data);
    if($this->trial_id > 0 && isset($form['status'])){
      //体験授業予定の場合、体験授業のステータスも更新する
      Trial::where('id', $this->trial_id)->first()->update(['status' => $status]);
    }
    $tag_names = ['matching_decide_word', 'course_type', 'lesson', 'course_minutes'];
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
    if($status==="absence" || $status==="fix" || $status==="rest"){
      //absence = 全員欠席＝休講
      //rest = 全員休み＝休講
      //fix = 全員の予定確定
      UserCalendarMember::where('calendar_id', $this->id)->update(
        ['status' => $status ]
      );
    }
    //事務システムも更新
    $this->office_system_api("PUT");
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
  public function is_last_status(){
    if($this->status==="rest" || $this->status==="absence" || $this->status==="presence"){
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
  public function is_conflict($start_time, $end_time, $place='', $place_floor=''){
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
      if(!empty($place) && $this->is_same_place($place)===false){
        //場所が異なるのでスケジュール競合
        return true;
      }
      else if(!empty($place_floor) && $this->is_same_place("",$place_floor)===false){
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
  public function is_same_place($place='', $place_floor=''){
    //場所のチェック　フロアから所在地を出して、所在地単位でチェックする
    if(!empty($place)){
      $calendar_place = $this->get_place($this->place);
      if($calendar_place==$place){
        return true;
      }
    }
    else if(!empty($place_floor)){
      $calendar_place = $this->get_place($this->place);
      $args_place = $this->get_place($place_floor);
      if($calendar_place==$args_place){
        return true;
      }
    }
    return false;
  }
  private function get_place($floor){
    foreach(config('lesson_place_floor') as $place => $floors){
      foreach($floors as $floor_code => $floor_name){
        if($floor_code==$floor){
          return $place;
        }
      }
    }
    return "";
  }
  public function office_system_api($method){
    $res = null;
    foreach($this->members as $member){
      $res = $member->office_system_api($method);
      if($res=="null" || !isset($res["status"])) break;
      if($res["status"]!=0) break;
    }
    return $res;
  }
}
