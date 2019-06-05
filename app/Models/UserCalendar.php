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
  public function user(){
    return $this->belongsTo('App\User');
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
  public function tags(){
    return $this->hasMany('App\Models\UserCalendarTag', 'calendar_id');
  }
  public function setting(){
    return $this->belongsTo('App\Models\UserCalendarSetting', 'user_calendar_setting_id');
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
  public function scopeSearchWord($query, $word)
  {
    $search_words = explode(' ', $word);
    $where_raw = <<<EOT
      user_calendars.id in (select calendar_id where user_calendar_members um
        left join students s on s.user_id = um.user_id
        left join teachers t on t.user_id = um.user_id
        left join managers m on m.user_id = um.user_id
        where s.name_last like '%%'
EOT;
    $query = $query->where(function($query)use($search_words, $where_raw){
      foreach($search_words as $_search_word){
        $query = $query->whereRaw($where_raw,[$_search_word]);
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
  public function scopeFindWorks($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'work', $vals, $is_not);
  }
  public function scopeFindPlaces($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'place', $vals, $is_not);
  }
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
    if($is_not===true){
      $query = $query->whereNotIn($field, $vals);
    }
    else {
      $query = $query->whereIn($field, $vals);
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
  public function scopeFindExchangeTarget($query, $user_id, $lesson)
  {
    $from = date("Y-m-01 00:00:00", strtotime("-1 month "));
    $to = date("Y-m-01", strtotime("+2 month ".$from));
    //先月～今月末の対象生徒が、休みかつ、規定回数以上ではない
    //かつ、振替が未登録
    $query = $this->scopeSearchDate($query, $from, $to);
    $where_raw = <<<EOT
      user_calendars.id not in (select exchanged_calendar_id from user_calendars)
      and user_calendars.id in (
        select calendar_id from user_calendar_members where
          user_id = $user_id
          and status = 'rest'
          and remark != '規定回数以上'
        )
        and user_calendars.id in (
          select calendar_id from user_calendar_tags where
            tag_value = $lesson
            and tag_key = 'lesson'
          )
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }
  public function get_access_member($user_id){
    $user = User::where('id', $user_id)->first();
    if(isset($user)) $user = $user->details();
    $ret = [];
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
  public function get_attribute_name($key, $val){
    $item = GeneralAttribute::findKeyValue($key,$val)->first();
    if(isset($item)) return $item->attribute_name;
    return "";
  }
  public function lesson(){
    return $this->get_tag_name('lesson');
  }
  public function course(){
    return $this->get_tag_name('course_type');
  }
  public function course_minutes(){
    return $this->get_tag_name('course_minutes');
  }
  public function place(){
    if(!isset($this->place)) return "";
    return $this->get_attribute_name('lesson_place_floor', $this->place);
  }
  public function work(){
    if(!isset($this->work)) return "";
    return $this->get_attribute_name('work', $this->work);
  }
  public function teaching_name(){
    if($this->is_teaching()){
      if($this->trial_id > 0){
        return "体験授業";
      }
      if(intval($this->user_calendar_setting_id) > 0){
        return "通常授業";
      }
      if($this->exchanged_calendar_id > 0){
        return "振替授業";
      }
      return "追加授業";
    }
    return "";
  }
  public function status_name(){
    $status_name = "";
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
    if($this->work==9) return true;
    return false;
  }
  public function is_group(){
    /*
    $tag =  $this->get_tag('course_type');
    if($tag->tag_value=="group") return true;
    */
    $students = $this->get_students(1);
    //course_typeに限らず、生徒が複数いるかどうか
    if(count($students) > 1) return true;
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
    $item['work_name'] = $this->work();

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
      if($_member->role === 'manager'){
        $manager_name.=$_member['name'].',';
        $managers[] = $member;
      }
    }
    if($user_id > 0){
      //グループレッスンの場合など、ユーザーがアクセス可能な生徒を表示する
      foreach($this->get_access_member($user_id) as $member){
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
    $item['manager_name'] = trim($manager_name,',');
    $item['user_name'] = $this->user->details()->name();
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
    //TODO : グループレッスンの予定確定方式
    //A案：一人でも出席なら出席
    //B案：全員が出席なら出席（通知がかなり複雑）
    $status = $this->status;
    if(isset($form['status'])){
      $is_status_update = true;
      if($form['status']=='rest' || $form['status']=='cancel'){
        //status=restは生徒全員が休みの場合
        //status=fixは生徒全員が予定確認した場合
        //status=cancelは生徒全員が予定キャンセルした場合
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
    if($status==="absence" || $status==="rest" || $status==="confirm"){
      //absence = 全員欠席＝休講
      //rest = 全員休み＝休講
      //confirm = 全員の予定確認中に変更
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
  public function is_teaching(){
    switch(intval($this->work)){
      case 6:
      case 7:
      case 8:
        return true;
    }
    return false;
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
      if(!isset($res)) continue;
      if($res=="null" || !isset($res["status"])) break;
      if($res["status"]!=0) break;
    }
    return $res;
  }
  /*
  public function student_mail($title, $param, $type, $template,$user_id=0){
    \Log::info("-----------------student_mail start------------------");
    $param['send_to'] = 'student';
    foreach($this->members as $member){
      if($member->user->details('students')->role != "student") continue;
      \Log::info('user_id='.$member->user_id.":student_id=".$member->user->student->id);
      //if($student_id >0 && $student_id != $member->user->student->id) continue;
      $member->send_mail($title, $param, $type, $template);
    }
    \Log::info("-----------------student_mailend------------------");
    return;
  }
  public function all_member_mail($title, $param, $type, $template){
    \Log::info("-----------------all_member_mail start------------------");
    \Log::info($title);
    //$this->teacher_mail($title, $param, $type, $template);
    $this->student_mail($title, $param, $type, $template);
    \Log::info("-----------------all_member_mail end------------------");
    return;
  }
  */
  public function teacher_mail($title, $param, $type, $template){
    $param['send_to'] = 'teacher';
    $param['item'] = $this->details(1);
    foreach($this->members as $member){
      if($member->user->details('teachers')->role != "teacher") continue;
      $member->send_mail($title, $param, $type, $template);
    }
    return;
  }
  public function get_students($user_id=0){
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
}
