<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;

class Trial extends Model
{
  protected $table = 'trials';
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
  public function has_tag($key, $val){
    $tags = $this->tags;
    foreach($tags as $tag){
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
    $item['parent_name'] =  $this->parent->name();
    $item['parent_phone_no'] =  $this->parent->phone_no;
    $item['parent_address'] =  $this->parent->address;
    $item['parent_email'] =  $this->parent->user->email;
    $item['date1'] = date('m月d日 H:i',  strtotime($this->trial_start_time1)).'～'.$item['trial_end1'];
    $item['date2'] = date('m月d日 H:i',  strtotime($this->trial_start_time2)).'～'.$item['trial_end2'];
    $subject1 = [];
    $subject2 = [];
    $tagdata = [];
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 10){
          $subject2[]= $tag->keyname();
        }
        else if(intval($tag->tag_value) > 1){
          $subject1[] = $tag->keyname();
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
    foreach($this->user_calendar_settings as $user_calendar_setting){
      $calendar_settings[] = $user_calendar_setting->details();
    }
    $item['calendar_settings'] = $calendar_settings;
    return $item;
  }
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
    //保護者情報登録
    //同じ送信内容の場合は登録しない
    $parent = StudentParent::entry($form);
    $form["create_user_id"] = $parent->user_id;
    $parent = $parent->profile_update($form);
    $form["kana_last"] = $form["student_kana_last"];
    $form["kana_first"] = $form["student_kana_first"];
    $form["name_last"] = $form["student_name_last"];
    $form["name_first"] = $form["student_name_first"];
    //生徒情報登録
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
    if(!isset($form['remark']) || empty($form['remark'])) $form['remark'] = '';
    $this->update([
      'remark' => $form['remark'],
      'trial_start_time1' => $form['trial_start_time1'],
      'trial_end_time1' => $form['trial_end_time1'],
      'trial_start_time2' => $form['trial_start_time2'],
      'trial_end_time2' => $form['trial_end_time2'],
    ]);
    $tag_names = ['howto_word', 'course_type', 'piano_level', 'english_teacher', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes'];
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
    $tag_names = ['lesson_place', 'howto', 'lesson', 'kids_lesson', 'english_talk_lesson'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        TrialTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
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
      'place' => $form['lesson_place_floor'],
      'lesson' => $form['lesson'],
      'course_type' => $form['course_type'],
      'remark' => $this->remark,
      'matching_decide_word' => $form['matching_decide_word'],
      'matching_decide' => $form['matching_decide'],
      'exchanged_calendar_id' => 0,
      'teacher_user_id' => $teacher->user_id,
    ];
    $charge_student_form = [
      'schedule_method' => 'month',
      'lesson_week_count' => 0,
      'lesson_week' => '',
      'teacher_id' => $teacher->id,
      'from_time_slot' => date('H:i:s', strtotime($form["start_time"])),
      'to_time_slot' => date('H:i:s', strtotime($form["end_time"])),
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
    $teachers = Teacher::findStatuses('0');
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
    /*
    $start_hour1 = date('H',  strtotime($this->trial_start_time1));
    $end_hour1 = date('H',  strtotime($this->trial_end_time1));
    $start_hour2 = date('H',  strtotime($this->trial_start_time2));
    $end_hour2 = date('H',  strtotime($this->trial_end_time2));
    */
    //30分ごとの開始時間から1時間を指定
    $_start = $this->trial_start_time1;
    $time_list1 = [];
    while(1){
      $_end = date("Y-m-d H:i:s", strtotime("+".$course_minutes." minute ".$_start));
      if(strtotime($_end) > strtotime($this->trial_end_time1)){
        break;
      }
      $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $time_list1[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      $_start = date("Y-m-d H:i:s", strtotime("+30 minute ".$_start));
    }
    $_start = $this->trial_start_time2;
    $time_list2 = [];
    while(1){
      $_end = date("Y-m-d H:i:s", strtotime("+".$course_minutes." minute ".$_start));
      if(strtotime($_end) > strtotime($this->trial_end_time2)){
        break;
      }
      $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $time_list2[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      $_start = date("Y-m-d H:i:s", strtotime("+30 minute ".$_start));
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
        //科目が担当可能
        $now_calendars = UserCalendar::findUser($teacher->user_id)
                        ->findStatuses(['fix', 'confirm'])
                        ->searchDate($detail['trial_date1'].' 00:00:00', $detail['trial_date1'].' 23:59:59')
                        ->get();
        $teacher_trial1 = $time_list1;
        if(isset($now_calendars)){
          foreach($now_calendars as $now_calendar){
            foreach($teacher_trial1 as $i => $_time){
              if($now_calendar->is_conflict($_time['start_time'], $_time['end_time'])){
                $teacher_trial1[$i]['free'] = false;
                $teacher_trial1[$i]['calendar_id'] = $now_calendar->id;
              }
            }
          }
        }

        $now_calendars = UserCalendar::findUser($teacher->user_id)
                        ->findStatuses(['fix', 'confirm'])
                        ->searchDate($detail['trial_date2'].' 00:00:00', $detail['trial_date2'].' 23:59:59')
                        ->get();
        $teacher_trial2 = $time_list2;
        if(isset($now_calendars)){
          foreach($now_calendars as $now_calendar){
            foreach($teacher_trial2 as $i => $_time){
              if($now_calendar->is_conflict($_time['start_time'], $_time['end_time'])){
                $teacher_trial2[$i]['free'] = false;
                $teacher_trial2[$i]['calendar_id'] = $now_calendar->id;
              }
            }
          }
        }
        $match_schedule = $this->get_match_schedule($teacher);
        if($match_schedule['all_count'] >= 0){
          //$teacher->brother_schedule = $this->get_brother_schedule($teacher);
          $teacher->match_schedule = $match_schedule;
          $teacher->trial1 = $teacher_trial1;
          $teacher->trial2 = $teacher_trial2;
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
  public function get_match_schedule($teacher){
    if(empty($this->student_schedule)){
      $student = $this->trial_students->first()->student;
      $this->student_schedule = $student->user->get_lesson_times();
    }
    if($this->couser_minutes==0){
      if(!isset($student)) $student = $this->trial_students->first()->student;
      $this->course_minutes = intval($student->user->get_tag('course_minutes')['tag_value']);
    }
    $teacher_enable_schedule = $teacher->user->get_lesson_times();
    $teacher_current_schedule = $teacher->user->get_week_calendar_setting();
    $detail = [];
    $count = [];
    $all_count = 0;
    $from_time = "";
    $_minute = $this->course_minutes;
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
      foreach($week_schedule as $time => $val){
        $is_free = false;
        if(isset($teacher_enable_schedule) && isset($teacher_enable_schedule[$week_day])
          && isset($teacher_enable_schedule[$week_day][$time])){
            //講師にも同じ曜日・時間の希望がある（ベースのシフト希望）
            $is_free = $teacher_enable_schedule[$week_day][$time];
        }
        if($is_free===true){
          //現状の講師のカレンダー設定とブッキングしたらfalse
          if(isset($teacher_current_schedule) && isset($teacher_current_schedule[$week_day])
            && isset($teacher_current_schedule[$week_day][$time]) && $teacher_current_schedule[$week_day][$time]==true){
              //講師にも同じ曜日・時間の希望がある
              $is_free = false;
          }
        }
        //echo"[".$week_day."][".$time."][".$_minute."][".$is_free."]<br>";
        if($is_free===true){
          // 空き
          if(empty($from_time)){
            $from_time = $time;
          }
          $c+=30;
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
    return ["all_count" => $all_count, "count"=>$count, "detail" => $detail];
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
      'setting_id_org' => 0,
      'place' => $form['lesson_place_floor'],
      'remark' => '',
      'lesson' => $calendar->get_tag('lesson')->tag_value,
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

    $setting = UserCalendarSetting::add($calendar_setting);
    foreach($this->trial_students as $trial_student){
      $student = Student::where('id', $trial_student->student_id)->first();
      $setting->memberAdd($student->user_id, $form['create_user_id']);
    }
    return $setting;
  }

}
