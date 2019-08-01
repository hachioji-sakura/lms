<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;
use App\Models\Ask;

class Trial extends Model
{
  protected $table = 'lms.trials';
  protected $guarded = array('id');
  public static $rules = array(
      'student_parent_id' => 'required',
      'student_id' => 'required',
  );
  public $student_schedule = null;
  public $course_minutes = 0;
  public function tags(){
    return $this->hasMany('App\Models\TrialTag', 'trial_id');
  }
  public function user_calendar_settings(){
    return $this->hasMany('App\Models\UserCalendarSetting', 'trial_id');
  }
  public function trial_students(){
    return $this->hasMany('App\Models\TrialStudent', 'trial_id');
  }
  public function calendars(){
    //一つのトライアルをもとに複数のスケジュールに派生する（キャンセルなどもあるため）
    return $this->hasMany('App\Models\UserCalendar');
  }
  public function parent(){
    return $this->belongsTo('App\Models\StudentParent', 'student_parent_id');
  }
  /**
   *　スコープ：ステータス
   */
  public function scopeFindStatuses($query, $statuses)
  {
    $str_statuses = '';
    $statuses = explode(',', $statuses);
    foreach($statuses as $status){
      $str_statuses .= "'".$status."',";
    }
    $str_statuses = trim($str_statuses, ',');
    $where_raw = <<<EOT
      $this->table.status in ($str_statuses)
EOT;
    return $query->whereRaw($where_raw,[]);
  }

  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeSearchWord($query, $word)
  {
    $search_words = explode(' ', $word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('remark','like', $_like);
      }
    });
    return $query;
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
    return "";
  }
  public function get_tags($key){
    $item = $this->tags->where('tag_key', $key);
    if(isset($item)){
      return $item;
    }
    return null;
  }
  public function status_name(){
    if(app()->getLocale()=='en') return $this->status;
    $status_name = "";
    switch($this->status){
      case "complete":
        return "入会案内済み";
      case "new":
        return "未対応";
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
  public function details(){
    $item = $this;
    $item['status_name'] = $this->status_name();
    $item['create_date'] = date('Y/m/d',  strtotime($this->created_at));

    $item['trial_date1'] = date('Y/m/d',  strtotime($this->trial_start_time1));
    $item['trial_start1'] = date('H:i',  strtotime($this->trial_start_time1));
    $item['trial_end1'] = date('H:i',  strtotime($this->trial_end_time1));

    $item['trial_date2'] = date('Y/m/d',  strtotime($this->trial_start_time2));
    $item['trial_start2'] = date('H:i',  strtotime($this->trial_start_time2));
    $item['trial_end2'] = date('H:i',  strtotime($this->trial_end_time2));

    $item['trial_date3'] = date('Y/m/d',  strtotime($this->trial_start_time3));
    $item['trial_start3'] = date('H:i',  strtotime($this->trial_start_time3));
    $item['trial_end3'] = date('H:i',  strtotime($this->trial_end_time3));

    $item['parent_name'] =  $this->parent->name();
    $item['parent_phone_no'] =  $this->parent->phone_no;
    $item['parent_address'] =  $this->parent->address;
    $item['parent_email'] =  $this->parent->user->email;
    $item['date1'] = date('m月d日 H:i',  strtotime($this->trial_start_time1)).'～'.$item['trial_end1'];
    $item['date2'] = date('m月d日 H:i',  strtotime($this->trial_start_time2)).'～'.$item['trial_end2'];
    $item['date3'] = date('m月d日 H:i',  strtotime($this->trial_start_time3)).'～'.$item['trial_end3'];
    $subject1 = [];
    $subject2 = [];
    $tagdata = [];
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 10){
          $subject1[]= $tag->keyname();
        }
        else if(intval($tag->tag_value) > 1){
          $subject2[] = $tag->keyname();
        }
      }
      else {
        $tagdata[$tag->tag_key][$tag->tag_value] = $tag->name();
      }
    }

    $item['subject1'] = $subject1;
    $item['subject2'] = $subject2;
    $item['tagdata'] = $tagdata;
    $calendars = $this->get_calendar();
    foreach($calendars as $i => $calendar){
      $calendars[$i] = $calendar->details();
    }
    $item['calendars'] = $calendars;
    $calendar_settings = [];
    $user_calendar_settings = UserCalendarSetting::findTrialStudent($this->id)->orderByWeek()->get();
    foreach($user_calendar_settings as $user_calendar_setting){
      $calendar_settings[] = $user_calendar_setting->details();
    }
    $item['calendar_settings'] = $calendar_settings;
    return $item;
  }
  /**
   * 体験授業申し込み登録
   */
  static public function entry($form){
    $form["accesskey"] = '';
    $form["password"] = 'sakusaku';
    /*
    $form["name_last"] = $form["parent_name_last"];
    $form["name_first"] = $form["parent_name_first"];
    $form["kana_last"] = $form["parent_kana_last"];
    $form["kana_first"] = $form["parent_kana_first"];
    */
    $form["name_last"] = "";
    $form["name_first"] = "";
    $form["kana_last"] = "";
    $form["kana_first"] = "";
    //１．保護者情報登録：無名のアカウント＋電話番号、メールアドレスを保存
    //同じ送信内容の場合は登録しない
    $parent = StudentParent::entry($form);
    $form["create_user_id"] = $parent->user_id;
    $parent = $parent->profile_update($form);
    $form["kana_last"] = $form["student_kana_last"];
    $form["kana_first"] = $form["student_kana_first"];
    $form["name_last"] = $form["student_name_last"];
    $form["name_first"] = $form["student_name_first"];
    //２．生徒情報登録：氏名・カナ・学年・学校名
    //同じ送信内容の場合は登録しない
    $student = $parent->brother_add($form, 1);
    $ret = [];

    if(!empty($form['student2_name_last'])){
      //兄弟2人目
      $form["kana_last"] = $form["student2_kana_last"];
      $form["kana_first"] = $form["student2_kana_first"];
      $form["name_last"] = $form["student2_name_last"];
      $form["name_first"] = $form["student2_name_first"];
      $form["gender"] = $form["student2_gender"];
      $form["grade"] = $form["student2_grade"];
      $form["school_name"] = $form["student2_school_name"];
      $student2 = $parent->brother_add($form, 1);
    }
    if(!empty($form['student3_name_last'])){
      //兄弟３人目
      $form["kana_last"] = $form["student3_kana_last"];
      $form["kana_first"] = $form["student3_kana_first"];
      $form["name_last"] = $form["student3_name_last"];
      $form["name_first"] = $form["student3_name_first"];
      $form["gender"] = $form["student3_gender"];
      $form["grade"] = $form["student3_grade"];
      $form["school_name"] = $form["student3_school_name"];
      $student3 = $parent->brother_add($form, 1);
    }

    //申し込み情報登録
    $trial = Trial::where('student_parent_id', $parent->id)
    ->where('status', '!=' ,'cancel')
    ->where('status', '!=' ,'rest')
    ->first();

    //同じ人からの内容の場合は(cancel以外)登録しない
    if(!isset($trial)){
    }
    else {
      //同じ人からの申し込み
    }
    $trial = Trial::create([
      'student_parent_id' => $parent->id,
      'create_user_id' => $form['create_user_id'],
    ]);

    $trial_student = TrialStudent::create([
      'trial_id' => $trial->id,
      'student_id' => $student->id,
    ]);
    if(isset($student2)){
      $trial_student2 = TrialStudent::create([
        'trial_id' => $trial->id,
        'student_id' => $student2->id,
      ]);
    }
    if(isset($student3)){
      $trial_student3 = TrialStudent::create([
        'trial_id' => $trial->id,
        'student_id' => $student3->id,
      ]);
    }
    //申し込み情報更新
    //同じ送信内容の場合は、申し込み情報のみ更新する
    $trial->trial_update($form);

    return $trial;
  }
  public function trial_update($form){
    $form['trial_start_time1'] = $form['trial_date1'].' '.$form['trial_start_time1'].':00:00';
    $form['trial_end_time1'] = $form['trial_date1'].' '.$form['trial_end_time1'].':00:00';

    $form['trial_start_time2'] = $form['trial_date2'].' '.$form['trial_start_time2'].':00:00';
    $form['trial_end_time2'] = $form['trial_date2'].' '.$form['trial_end_time2'].':00:00';

    $form['trial_start_time3'] = $form['trial_date3'].' '.$form['trial_start_time3'].':00:00';
    $form['trial_end_time3'] = $form['trial_date3'].' '.$form['trial_end_time3'].':00:00';

    if(!isset($form['remark']) || empty($form['remark'])) $form['remark'] = '';
    $this->update([
      'remark' => $form['remark'],
      'trial_start_time1' => $form['trial_start_time1'],
      'trial_end_time1' => $form['trial_end_time1'],
      'trial_start_time2' => $form['trial_start_time2'],
      'trial_end_time2' => $form['trial_end_time2'],
      'trial_start_time3' => $form['trial_start_time3'],
      'trial_end_time3' => $form['trial_end_time3'],
    ]);
    $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'english_talk_lesson']; //生徒のuser_tagと共通
    $tag_names[] ='howto'; //体験のみのタグ
    //通塾可能曜日・時間帯タグ
    $lesson_weeks = GeneralAttribute::findKey('lesson_week')->get();
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'lesson_'.$lesson_week['attribute_value'].'_time';
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        TrialTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['piano_level', 'english_teacher', 'lesson_week_count', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes'
      ,'howto_word', 'course_type'];
    //科目タグ
    $charge_subject_level_items = GeneralAttribute::findKey('charge_subject_level_item')->get();
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        TrialTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    foreach($this->trial_students as $trial_student){
      $trial_student->student->profile_update($form);
    }
  }
  public function get_calendar(){
    $calendar = UserCalendar::where('trial_id', $this->id)->findStatuses(['cancel'], true)->get();
    return $calendar;
  }
  public function trial_to_calendar($form){
    $teacher = Teacher::where('id', $form['teacher_id'])->first();
    //$calendar = $this->get_calendar();
    //１トライアル複数授業予定のケースもある
    $calendar_form = [
      'start_time' =>  $form["start_time"],
      'end_time' =>  $form["end_time"],
      'trial_id' => $this->id,
      'place_floor_id' => $form['place_floor_id'],
      'lesson' => $form['lesson'],
      'course_type' => $form['course_type'],
      'remark' => $this->remark,
      'matching_decide_word' => $form['matching_decide_word'],
      'matching_decide' => $form['matching_decide'],
      'exchanged_calendar_id' => 0,
      'teacher_user_id' => $teacher->user_id,
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
    $calendar = UserCalendar::add($calendar_form);
    //体験同時申し込み生徒数だけ追加
    foreach($this->trial_students as $trial_student){
      $calendar->memberAdd($trial_student->student->user_id, $form['create_user_id']);
      $charge_student_form['student_id'] = $trial_student->student->id;
      ChargeStudent::add($charge_student_form);
    }

    return $calendar;
  }
  public function candidate_teachers($teacher_id, $lesson){
    $lessons = $this->tags->where('tag_key', 'lesson');
    if($teacher_id > 0 && $lesson > 0){
      return $this->_candidate_teachers($teacher_id, $lesson);
    }
    $ret = [];
    foreach($lessons as $lesson){
      $_candidate_teachers = $this->_candidate_teachers($teacher_id, $lesson->tag_value);
      if(isset($_candidate_teachers) && count($_candidate_teachers) > 0){
        $ret[$lesson->tag_value] = $_candidate_teachers;
      }
    }
    return $ret;
  }
  public function _candidate_teachers($teacher_id=0, $lesson=0){
    $detail = $this->details();
    //体験希望科目を取得
    $trial_subjects = [];
    $kids_lesson = [];
    $english_talk_lesson = [];
    $course_minutes = 0;
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 1){
          //希望科目のタグのみ集める
          $trial_subjects[$tag->tag_key] = $tag;
        }
      }
      if($tag->tag_key==='course_minutes'){
        $course_minutes = $tag->tag_value;
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
        $teachers = $teachers->chargeSubject($trial_subjects);
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
    //30分ごとの開始時間から授業時間までのslotを作成
    $time_list1 = $this->get_time_list($this->trial_start_time1, $this->trial_end_time1, $course_minutes);
    $time_list2 = $this->get_time_list($this->trial_start_time2, $this->trial_end_time2, $course_minutes);
    $time_list3 = $this->get_time_list($this->trial_start_time3, $this->trial_end_time3, $course_minutes);
    $ret = [];
    foreach($teachers as $teacher){
      $enable_point = 0;
      $disable_point = 0;
      $disable_subject = [];
      $enable_subject = [];
      if($lesson==1){
        $charge_subjects = $teacher->get_charge_subject();
        //塾の場合、担当可能、不可能な科目の情報セットを作る
        foreach($trial_subjects as $tag_key  => $tag){
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
          //$teacher->brother_schedule = $this->get_brother_schedule($teacher);
          $teacher->match_schedule = $match_schedule;
          /*
          $teacher->trial1 = $teacher_trial1;
          $teacher->trial2 = $teacher_trial2;
          */
          $teacher->trial = $this->get_time_list_free($time_list1, $teacher->user_id, $detail['trial_date1'], "trial_date1");
          $teacher->trial = array_merge($teacher->trial, $this->get_time_list_free($time_list2, $teacher->user_id, $detail['trial_date2'], "trial_date2"));
          $teacher->trial = array_merge($teacher->trial, $this->get_time_list_free($time_list3, $teacher->user_id, $detail['trial_date3'], "trial_date3"));
          $teacher->trial = $this->get_time_list_review($teacher->user_id, $teacher->trial);
          $teacher->enable_point = $enable_point;
          $teacher->disable_point = $disable_point;
          $teacher->subject_review = 'part';
          if($disable_point < 1) $teacher->subject_review = 'all';
          $teacher->enable_subject = $enable_subject;
          $teacher->disable_subject = $disable_subject;
          $ret[] = $teacher;
        }

      }
    }

    return $ret;
  }
  //↓まだつかってない
  public function get_brother_schedule($teacher){
    $student = [];
    foreach($this->trial_students as $i => $trial_student){
      $calendar = UserCalendar::searchDate(date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('+14 day')))
        ->findUser($teacher->user_id)
        ->findUser($trial_student->student->user_id)
        ->where('trial_id', '>', 0)
        ->get();
      $students[$i] = $trial_student->student;
      $students[$i]->current_schedule = $calendar;
    }
    return $students;
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
    if($form['action'] == 'new'){
      $setting = UserCalendarSetting::add($calendar_setting);
    }
    else {
      $setting = UserCalendarSetting::where('id', $form['calendar_setting_id'])->first();
    }
    if(isset($setting)){
      foreach($this->trial_students as $trial_student){
        $student = Student::where('id', $trial_student->student_id)->first();
        $setting->memberAdd($student->user_id, $form['create_user_id']);
      }
    }
    return $setting;
  }
  private function get_time_list($trial_start_time, $trial_end_time, $course_minutes){
    $_start = $trial_start_time;
    $time_list = [];
    while(1){
      $_end = date("Y-m-d H:i:s", strtotime("+".$course_minutes." minute ".$_start));
      if(strtotime($_end) > strtotime($trial_end_time)){
        break;
      }
      $_duration = date('H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $status = "free";
      foreach($this->get_calendar() as $calendar){
        if($calendar->is_conflict($_start, $_end)){
          $status = "trial_calendar_conflict";
          break;
        }
      }
      $time_list[]=['start_time' => $_start, 'end_time' => $_end, 'status' => $status, 'duration' => $_duration];
      $_start = date("Y-m-d H:i:s", strtotime("+10 minute ".$_start));
    }
    return $time_list;
  }
  private function get_time_list_free($time_list, $teacher_user_id, $trial_date, $remark=""){
    /*
    if(strtotime("now") > strtotime($trial_date)){
      return [];
    }
    */
    //講師の対象日のカレンダーを取得
    $now_calendars = UserCalendar::findUser($teacher_user_id)
                    ->findStatuses(['fix', 'confirm'])
                    ->searchDate($trial_date.' 00:00:00', $trial_date.' 23:59:59')
                    ->get();
    $_time_list = $time_list;
    foreach($_time_list as $i => $_time){
      $status = "free";
      $_time_list[$i]["conflict_calendar"] = null;
      $_time_list[$i]["is_time_conflict"] = false;
      $_time_list[$i]["is_place_conflict"] = false;
      $_time_list[$i]["free_place_floor"] = "";
      $_time_list[$i]["review"]="";
      $_time_list[$i]["remark"]=$remark;;
      if(isset($now_calendars)){
        //講師の予定との競合するかチェック
        foreach($now_calendars as $now_calendar){
          if($_time_list[$i]['is_time_conflict']===false){
            if($now_calendar->is_conflict($_time['start_time'], $_time['end_time'])){
              //時間が競合した場合
              $_time_list[$i]['conflict_calendar'] = $now_calendar;
              $_time_list[$i]['is_time_conflict'] = true;
            }
          }
          if($_time_list[$i]['is_time_conflict']===false && $_time_list[$i]['is_place_conflict']===false){
            //echo "競合チェック：pass:[".$_time_list[$i]["duration"]."][".$tag->tag_value."][".$now_calendar->place."]";
            $_free_place = "";
            $is_place_conflict = true;
            foreach($this->get_tags('lesson_place') as $tag){
              if(!$now_calendar->is_conflict($_time['start_time'], $_time['end_time'], $tag->tag_value)){
                //場所の競合もなし=選択候補
                $is_place_conflict = false;
                if($now_calendar->is_same_place($tag->tag_value)){
                  $_free_place = $now_calendar->place;
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
  //primaryがついている場合、primaryのみに選択肢を削る
  private function get_time_list_review($user_id, $_time_list){
    $primary_count = 0;
    //予定ありの次の時間の状態によって評価を設定
    for($i=0;$i<count($_time_list);$i++){
      $review = $this->schedule_review($user_id, $_time_list[$i])["review"];
      if($i>0 && $review!="primary"){
        $_review = $this->schedule_review($user_id, $_time_list[$i])["review"];
        if($_review=="primary") $review = $_review;
      }
      if($review=="primary") $primary_count++;
      $_time_list[$i]["review"] = $review;
    }
    //return $_time_list;
    //優先度の最も高いものから返却する
    $ret = [];
    foreach($_time_list as $_item){
      $is_add = false;
      if($primary_count > 0){
        if($_item["review"]==="primary"){
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
    $ret = ['calendar'=>null, 'same_place'=>'', 'review'=>''];
    if($target["status"]!=="free") return $ret;
    //上に隣接する授業設定を取得
    $prev_calendars = UserCalendar::where('user_id', $user_id)
                      ->where('start_time', $target["end_time"])
                      ->get();
    $prev = null;
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
    //下に隣接する授業設定を取得
    $next_calendars = UserCalendar::where('user_id', $user_id)
                      ->where('end_time', $target["start_time"])
                      ->get();
    $next = null;
    foreach($next_calendars as $calendar){
      $same_place = "";
      foreach($this->get_tags('lesson_place') as $tag){
        if($calendar->is_same_place($tag->tag_value)){
          $next  = $calendar;
          return $ret;
        }
        if(isset($next)) break;
      }
      if(isset($next)) break;
    }
    if( (isset($prev) && isset($next)) ||
         (isset($prev) && !isset($next) && count($next_calendars)<1)){
       //１．上下隣接、２．上に隣接し下がない
       $ret['calendar'] = $prev;
       $ret['same_place'] = $prev->place_floor_id;
       $ret['review'] = 'primary';
    }
    else if(!isset($prev) && isset($next) && count($prev_calendars)<1){
      //３．下に隣接し上がない
      $ret['calendar'] = $next;
      $ret['same_place'] = $next->place_floor_id;
      $ret['review'] = 'primary';
    }
    return $ret;
  }

  //体験希望スケジュールと、講師の勤務可能スケジュール・現在のスケジュール設定
  public function get_match_schedule($teacher){
    //１．この体験対象の生徒の希望スケジュールと希望授業時間を取得
    if(empty($this->student_schedule)){
      //兄弟登録された場合は一人目と同一のため、一人目のスケジュールを利用する
      $student = $this->trial_students->first()->student;
      $this->student_schedule = $student->user->get_lesson_times(10);
    }
    if($this->couser_minutes==0){
      if(!isset($student)) $student = $this->trial_students->first()->student;
      $this->course_minutes = intval($student->user->get_tag('course_minutes')['tag_value']);
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
        $data = ["week_day"=>$week_day, "from" => $time, "to"=>"", "status"=>"free", "review"=>"", "place"=>"", "show"=>false];

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

        //３－３．現状の講師のカレンダー設定とブッキングしたらfalse
        if($is_free===true){
          $f = date('H:i:00', strtotime('2000-01-01 '.$time.'00'));
          $t = date('H:i:00', strtotime('+'.$this->course_minutes.'minute 2000-01-01 '.$time.'00'));
          foreach($teacher->user->calendar_settings as $setting){
            if($setting->is_conflict_setting($week_day, $f,$t)==true){
              //echo "conflict:".$week_day.'/'.$f."-".$t." / ".$setting->from_time_slot."-".$setting->to_time_slot."<br>";
              $is_free = false;
              $data["status"] = "time_conflict";
              $data["conflict_calendar_setting"] = $setting;
              break;
            }
          }
          /*
          if(isset($teacher_current_schedule) && isset($teacher_current_schedule[$week_day])
            && isset($teacher_current_schedule[$week_day][$time]) && isset($teacher_current_schedule[$week_day][$time]->id)){
              $is_free = false;
              $data["status"] = "time_conflict";
              $data["conflict_calendar_setting"] = $teacher_current_schedule[$week_day][$time];
          }
          */
        }
        //echo"[".$week_day."][".$time."][".$this->course_minutes."][".$is_free."]<br>";
        if($is_free===true){
          // 空き
          if(empty($from_time)){
            //どこからどこまでの時間が空いているか記録
            $from_time = $time;
          }
          $c+=10;
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
        $student_schedule[$week_day][] = $data;
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
    $primary_count = 0;
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
          if($review['review']=='place_conflict'){
            $student_schedule[$week_day][$i]["status"] = "place_conflict";
          }
          else if($review['review']=='primary'){
            $student_schedule[$week_day][$i]["review"] = "primary";
            $student_schedule[$week_day][$i]["place"] = $review['same_place'];
            $primary_count++;
          }
        }

        //echo "[$week_day][$c][".$student_schedule[$week_day][$i]["from"]."][".$student_schedule[$week_day][$i]["status"]."][".$student_schedule[$week_day][$i]["review"]."]<br>";
      }
    }
    //優先度に応じた、設定可能な曜日×開始時間リストを返す
    foreach($student_schedule as $week_day => $week_schedule){
      $c=0;
      for($i=0;$i<count($week_schedule);$i++){
        if($week_schedule[$i]["status"]!=="free") continue;
        $c++;
        if($primary_count > 0){
          if($week_schedule[$i]["review"]==="primary") {
            $student_schedule[$week_day][$i]["show"] = true;
          }
        }
        else {
          $student_schedule[$week_day][$i]["show"] = true;
        }
        //echo $week_day."[".$student_schedule[$week_day][$i]["from"]."][".$student_schedule[$week_day][$i]["review"]."][".$student_schedule[$week_day][$i]["show"]."]<br>";
      }
      $count[$week_day] = intval($c/$minute_count);
    }
    return ["all_count" => $all_count, "count"=>$count, "detail" => $detail, "result" => $student_schedule];
  }
  //コマの評価づけ：隣接するスケジュールを優先とする
  private function get_setting_review($user_id, $lesson_week, $from_time_slot, $to_time_slot){
    //echo "[".$lesson_week."][".$from_time_slot."][".$to_time_slot."]<br>";
    $is_place_conflict = false;
    $ret = ['setting'=>null, 'same_place'=>'', 'review'=>''];
    //上に隣接する通常授業設定を取得
    $prev_settings = UserCalendarSetting::where('user_id', $user_id)
                      ->where('from_time_slot', $to_time_slot)
                      ->where('from_time_slot', $to_time_slot)
                      ->where('lesson_week', $lesson_week)
                      ->get();
    $prev = null;
    foreach($prev_settings as $setting){
      foreach($this->get_tags('lesson_place') as $tag){
        //体験希望所在地のフロアと同じ場合隣接とみなす
        if($setting->is_same_place($tag->tag_value)==true){
          $prev  = $setting;
        }
        else {
          $is_place_conflict = true;
        }
        if(isset($prev)) break;
      }
      if(isset($prev)) break;
    }
    //下に隣接する通常授業設定を取得
    $next_settings = UserCalendarSetting::where('user_id', $user_id)
                      ->where('to_time_slot', $from_time_slot)
                      ->where('lesson_week', $lesson_week)
                      ->get();
    $next = null;
    foreach($next_settings as $setting){
      foreach($this->get_tags('lesson_place') as $tag){
        if($setting->is_same_place($tag->tag_value)==true){
          $next  = $setting;
        }
        else {
          $is_place_conflict = true;
        }
        if(isset($next)) break;
      }
      if(isset($next)) break;
    }
    if($is_place_conflict==true){
      $ret['review'] = 'place_conflict';
    }
    else if( (isset($prev) && isset($next)) ||
         (isset($prev) && !isset($next) && count($next_settings)<1)){
       //１．上下隣接、２．上に隣接し下がない
       $ret['setting'] = $prev;
       $ret['same_place'] = $prev->place_floor_id;
       $ret['review'] = 'primary';
    }
    else if(!isset($prev) && isset($next) && count($prev_settings)<1){
      //３．下に隣接し上がない
      $ret['setting'] = $next;
      $ret['same_place'] = $next->place_floor_id;
      $ret['review'] = 'primary';
    }
    return $ret;
  }
  public function agreement_ask($create_user_id, $access_key){
    //この体験に関してはいったん完了ステータス
    //保護者にアクセスキーを設定
    $this->parent->user->update(['access_key' => $access_key]);
    $this->update(['status' => 'complete']);

    Ask::where('target_model', 'trials')->where('target_model_id', $this->id)
        ->where('status', 'new')->where('type', 'agreement')->delete();

    $ask = Ask::add("agreement", [
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
      //①通常授業スケジュールをfix
      foreach($this->trial_students as $trial_student){

      }
      //②受講料期限を更新し有効にする
      //③保護者に本登録依頼メール（入会手続き完了メール）
    }
  }
}
