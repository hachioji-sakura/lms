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
  public function student(){
    return $this->belongsTo('App\Models\Student');
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
    /*
    $form["name_first"] = $form["student_name_first"];
    $form["kana_last"] = $form["student_kana_last"];
    $form["kana_first"] = $form["student_kana_first"];
    */
    $form["name_last"] = $form["student_name_last"];
    $form["name_first"] = $form["student_name_first"];
    //生徒情報登録
    //同じ送信内容の場合は登録しない
    $student = $parent->brother_add($form, 1);
    $ret = [];

    //申し込み情報登録
    $trial = Trial::where('student_parent_id', $parent->id)
    ->where('student_id', $student->id)
    ->where('status', '!=' ,'cancel')
    ->where('status', '!=' ,'rest')
    ->first();

    //同じ人からの内容の場合は(cancel以外)登録しない
    if(!isset($trial)){
      $trial = Trial::create([
        'student_parent_id' => $parent->id,
        'student_id' => $student->id,
        'create_user_id' => $form['create_user_id'],
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
    $tag_names = ['howto_word', 'piano_level', 'english_teacher', 'course_type', 'course_minutes'];
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
    $tag_names = ['lesson_place', 'howto', 'lesson', 'kids_lesson'];
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
    $student = Student::where('id', $this->student_id)->first();
    //$calendar = $this->get_calendar();
    //１トライアル複数授業予定のケースもある
    $calendar = UserCalendar::add([
      'start_time' =>  $form["start_time"],
      'end_time' =>  $form["end_time"],
      'trial_id' => $this->id,
      'place' => $form['place'],
      'remark' => $this->remark,
      'exchanged_calendar_id' => 0,
      'teacher_user_id' => $teacher->user_id,
      'create_user_id' => $form['create_user_id'],
    ]);
    $calendar->memberAdd($student->user_id, $form['create_user_id']);
    $tag_names = ['matching_decide_word', 'charge_subject'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTag::setTag($calendar->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['matching_decide'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserCalendarTag::setTags($calendar->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    $charge_student_form = [
      'schedule_method' => 'month',
      'lesson_week_count' => 0,
      'lesson_week' => '',
      'teacher_id' => $teacher->id,
      'student_id' => $this->student_id,
      'create_user_id' => $form['create_user_id'],
      'from_time_slot' => date('H:i:s', strtotime($form["start_time"])),
      'to_time_slot' => date('H:i:s', strtotime($form["end_time"])),
    ];
    ChargeStudent::add($charge_student_form);

    return $calendar;
  }
  public function status_style(){
    $status_name = "";
    switch($this->status){
      case "confirm":
        return "warning";
      case "fix":
        return "primary";
    }
    return "secondary";
  }
  public function status_name(){
    $status_name = "";
    switch($this->status){
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
    $item['status_style'] = $this->status_style();
    $item['create_date'] = date('Y/m/d',  strtotime($this->created_at));
    $item['trial_date1'] = date('Y/m/d',  strtotime($this->trial_start_time1));
    $item['trial_start1'] = date('H:i',  strtotime($this->trial_start_time1));
    $item['trial_end1'] = date('H:i',  strtotime($this->trial_end_time1));
    $item['trial_date2'] = date('Y/m/d',  strtotime($this->trial_start_time2));
    $item['trial_start2'] = date('H:i',  strtotime($this->trial_start_time2));
    $item['trial_end2'] = date('H:i',  strtotime($this->trial_end_time2));
    $item['student_name'] = $this->student->name();
    $item['student_gender'] = $this->student->gender();
    $item['student_birth_day'] = $this->student->birth_day();
    $item['parent_name'] =  $this->parent->name();
    $item['parent_phone_no'] =  $this->parent->phone_no;
    $item['parent_email'] =  $this->parent->user->email;
    $item['date1'] = date('m月d日 H:i',  strtotime($this->trial_start_time1)).'～'.$item['trial_end1'];
    $item['date2'] = date('m月d日 H:i',  strtotime($this->trial_start_time2)).'～'.$item['trial_end2'];
    $item['grade'] = $this->student->grade();
    $item['school_name'] = $this->student->get_tag('school_name')['name'];
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
    return $item;
  }
  public function candidate_teachers($teacher_id=0){
    $detail = $this->details();
    //体験希望科目を取得
    $trial_subjects = [];
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 1){
          //希望科目のタグのみ集める
          $trial_subjects[$tag->tag_key] = $tag;
        }
      }
    }
    $teachers = Teacher::findStatuses('0,1')->chargeSubject($trial_subjects);
    //在籍する講師を取得
    if($teacher_id > 0){
      //講師選択済みの場合
      //対象選択条件は科目が担当可能なので、id指定は後から行う必要がある
      $teachers = $teachers->where('id', $teacher_id);
    }
    $teachers = $teachers->get();
    $start_hour1 = date('H',  strtotime($this->trial_start_time1));
    $end_hour1 = date('H',  strtotime($this->trial_end_time1));
    $start_hour2 = date('H',  strtotime($this->trial_start_time2));
    $end_hour2 = date('H',  strtotime($this->trial_end_time2));
    $time_list1 = [];
    for($i=$start_hour1;$i<$end_hour1;$i++){
      $_start = $detail['trial_date1'].' '.$i.':00:00';
      $_end = $detail['trial_date1'].' '.($i+1).':00:00';
      $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $time_list1[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      if($i==$end_hour1-2){
        $_start = $detail['trial_date1'].' '.$i.':30:00';
        $_end = $detail['trial_date1'].' '.($i+1).':30:00';
        $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
        $time_list1[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      }
    }
    for($i=$start_hour2;$i<$end_hour2;$i++){
      $_start = $detail['trial_date2'].' '.$i.':00:00';
      $_end = $detail['trial_date2'].' '.($i+1).':00:00';
      $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
      $time_list2[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      if($i==$end_hour2-2){
        $_start = $detail['trial_date2'].' '.$i.':30:00';
        $_end = $detail['trial_date2'].' '.($i+1).':30:00';
        $_dulation = date('m月d日 H:i', strtotime($_start)).'～'.date('H:i', strtotime($_end));
        $time_list2[]=["start_time" => $_start, "end_time" => $_end, "free" => true, "dulation" => $_dulation];
      }
    }
    $ret = [];
    foreach($teachers as $teacher){
      $charge_subjects = $teacher->get_charge_subject();
      $enable_point = 0;
      $disable_point = 0;
      $disable_subject = [];
      $enable_subject = [];

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
            $enable_point +=1;
          }
          else {
            //対応不可能：希望を下回る
            $disable_subject[$tag_key] = [
              "subject_key" => str_replace('_level', '', $tag_key),
              "subject_name" => $tag_keyname,  //科目名
              "level_name" => $tag_name, //補習可能、受験可能など
              "style" => "secondary",
            ];
            $disable_point +=1;
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
          $disable_point +=1;
        }
      }
      if($enable_point > 0){
        //科目の句補があれば、
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
        if($match_schedule['all_count'] > 0){
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
  public function get_match_schedule($teacher){
    if(empty($this->student_schedule)){
      $student = Student::where('id', $this->student_id)->first();
      $this->student_schedule = $student->user->get_lesson_times();
    }
    if($this->couser_minutes==0){
      if(!isset($student)) $student = Student::where('id', $this->student_id)->first();
      $this->course_minutes = intval($student->user->get_tag('course_minutes')['tag_value']);
    }
    $teacher_enable_schedule = $teacher->user->get_lesson_times();
    $teacher_current_schedule = $teacher->user->get_week_calendar_setting();
    $detail = [];
    $count = [];
    $all_count = 0;
    $from_time = "";
    foreach($this->student_schedule as $week_day => $week_schedule){
      $detail[$week_day] = [];
      $count[$week_day] = 0;
      $_minute = $this->course_minutes;
      $c = 0;
      foreach($week_schedule as $time => $val){
        $is_free = false;
        if(isset($teacher_enable_schedule) && isset($teacher_enable_schedule[$week_day])
          && isset($teacher_enable_schedule[$week_day][$time])){
            //講師にも同じ曜日・時間の希望がある
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
        //echo"[$week_day][$time]$_minute<br>";
        if($is_free===true){
          if(empty($from_time)){
            $from_time = $time;
          }
          $c+=30;
        }
        else {
          if(!empty($from_time)){
            //可能なコマがある場合カウントアップ
            $_slot = floor($c / $this->course_minutes);
            if($_slot > 0){
              $count[$week_day]+=$_slot;
              $all_count+=$_slot;
            }

            //直前まで連続していた
            $detail[$week_day][] = [
              "from" => $from_time,
              "to" => $time,
              "slot" => $_slot
            ];
          }
          $from_time = "";
          $c = 0;
          $_minute = $this->course_minutes;
        }
      }
      if(!empty($from_time)){
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
        $from_time = "";
      }
    }
    return ["all_count" => $all_count, "count"=>$count, "detail" => $detail];
  }
}
