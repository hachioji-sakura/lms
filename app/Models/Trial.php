<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\UserCalendar;

class Trial extends Model
{
  protected $table = 'trials';
  protected $guarded = array('id');
  public static $rules = array(
      'student_parent_id' => 'required',
      'student_id' => 'required',
  );
  public function tags(){
    return $this->hasMany('App\Models\TrialTag');
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
    $form["name_last"] = $form["parent_name_last"];
    $form["name_first"] = $form["parent_name_first"];
    $form["kana_last"] = $form["parent_kana_last"];
    $form["kana_first"] = $form["parent_kana_first"];
    //保護者情報登録
    //同じ送信内容の場合は登録しない
    $parent = StudentParent::entry($form);
    $form["create_user_id"] = $parent->user_id;
    $parent = $parent->profile_update($form);
    $form["name_last"] = $form["student_name_last"];
    $form["name_first"] = $form["student_name_first"];
    $form["kana_last"] = $form["student_kana_last"];
    $form["kana_first"] = $form["student_kana_first"];

    //生徒情報登録
    //同じ送信内容の場合は登録しない
    $student = $parent->brother_add($form);
    $ret = [];

    //申し込み情報登録
    $trial = Trial::where('student_parent_id', $parent->id)
      ->where('student_id', $student->id)
      ->first();
    //同じ送信内容の場合は登録しない
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

    $this->update([
      'remark' => $form['remark'],
      'trial_start_time1' => $form['trial_start_time1'],
      'trial_end_time1' => $form['trial_end_time1'],
      'trial_start_time2' => $form['trial_start_time2'],
      'trial_end_time2' => $form['trial_end_time2'],
    ]);
    $tag_names = ['howto_word'];
    $charge_subject_level_items = GeneralAttribute::findKey('charge_subject_level_item')->get();
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        TrialTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson_place', 'howto', 'lesson'];
    $lesson_weeks = GeneralAttribute::findKey('lesson_week')->get();
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'lesson_'.$lesson_week['attribute_value'].'_time';
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        TrialTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
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
        return "申込";
      case "fix":
        return "体験授業確定";
      case "cancel":
        return "キャンセル";
    }
    return "";
  }

  public function details(){
    $item = $this;
    $item['status_name'] = $this->status_name();
    $item['create_date'] = date('Y年m月d日',  strtotime($this->created_at));
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
    $item['grade'] = $this->student->get_tag('grade')['name'];
    $item['school_name'] = $this->student->get_tag('school_name')['name'];
    $item['subject1'] = "";
    $item['subject2'] = "";
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 10){
          $item['subject2'] .= $tag->keyname().',';
        }
        else if(intval($tag->tag_value) > 1){
          $item['subject1'] .= $tag->keyname().',';
        }
      }
      else {
        if(empty($item[$tag->tag_key])){
          $item[$tag->tag_key] = "";
        }
        $item[$tag->tag_key] .= $tag->name().',';
      }
    }
    return $item;
  }
  public function candidate_teachers(){

    //体験希望科目を取得
    $subjects_def = [];
    foreach($this->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        if(intval($tag->tag_value) > 1){
          $subjects_def[$tag->tag_key] = $tag;
        }
      }
    }
    //在籍する講師を取得
    $teachers = Teacher::findStatuses('0,1')->chargeSubject($subjects_def)->get();

    $ret = [];
    foreach($teachers as $teacher){
      $subjects = $teacher->get_charge_subject();
      $subject_point = 0;
      $disable_subject = [];
      $enable_subject = [];

      foreach($subjects_def as $tag_key  => $tag){
        $tag_val = intval($tag->tag_value);
        $tag_keyname = $tag->keyname();
        $tag_name = $tag->name();
        if(isset($subjects[$tag_key])){
          if($tag_val < intval($subjects[$tag_key])){
            $enable_subject[$tag_key] = [
              "key" => $tag_keyname,
              "name" => $tag_name,
              "style" => "primary",
            ];
            $subject_point +=2;
          }
          else if($tag_val == intval($subjects[$tag_key])){
            $enable_subject[$tag_key] = [
              "key" => $tag_keyname,
              "name" => $tag_name,
              "style" => "secondary",
            ];
            $subject_point +=1;
          }
          else {
            $disable_subject[$tag_key] = [
              "key" => $tag_keyname,
              "name" => $tag_name,
              "style" => "secondary",
            ];
          }
        }
        else {
          $disable_subject[$tag_key] = [
            "key" => $tag_keyname,
            "name" => $tag_name,
            "style" => "danger",
          ];
        }
      }
      if($subject_point > 0){
        $trial1_check = UserCalendar::findUser($teacher->user_id)
                        ->findStatuses(['fix', 'confirm'])
                        ->searchDate($this->trial_start_time1, $this->trial_end_time1)
                        ->first();
        if(isset($trial1_check)){
          $teacher->trial1 = $trial1_check->details();
        }
        $trial2_check = UserCalendar::findUser($teacher->user_id)
                        ->findStatuses(['fix', 'confirm'])
                        ->searchDate($this->trial_start_time2, $this->trial_end_time2)
                        ->first();
        if(isset($trial2_check)){
          $teacher->trial2 = $trial2_check->details();
        }

        $teacher->subject_point = $subject_point;
        $teacher->enable_subject = $enable_subject;
        $teacher->disable_subject = $disable_subject;
        $ret[] = $teacher;
      }
    }

    return $ret;
  }

}
