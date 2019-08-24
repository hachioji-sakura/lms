<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//データセット
use App\User;
use App\Models\Teacher;
use App\Models\UserTag;
//他
use App\Models\GeneralAttribute;

class Teacher extends Student
{
  protected $connection = 'mysql_common';
  protected $table = 'common.teachers';
  protected $guarded = array('id');

  public static $rules = array(
    'name_last' => 'required',
    'name_first' => 'required',
    'kana_last' => 'required',
    'kana_first' => 'required',
  );
  /**
   *　リレーション：担当生徒（担当講師）
   */
  public function chargeStudents(){
    return $this->hasMany('App\Models\ChargeStudent', 'teacher_id');
  }
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.teacher_status')[$this->status])){
      $status_name = config('attribute.teacher_status')[$this->status];
    }
    return $status_name;
  }

  public function scopeFindChargeStudent($query, $id)
  {
    $where_raw = <<<EOT
      $this->table.id in (select teacher_id from charge_students where student_id=?)
EOT;
    return $query->whereRaw($where_raw,[$id]);
  }
  public function scopeFindParent($query, $id)
  {
    return $query;
  }
  public function scopeChargeSubject($query, $subjects)
  {
    if(!isset($subjects)) return $query;
    if(count($subjects)<1) return $query;
    $where_raw = "";
    foreach($subjects as $subject){
      $key = $subject->tag_key;
      $value = intval($subject->tag_value);
      $_where_raw = <<<EOT
        $this->table.user_id in (select user_id from user_tags where tag_key='$key' and tag_value >= $value)
EOT;
      $where_raw .= 'OR ('.$_where_raw.')';
    }
    $where_raw = '('.trim($where_raw,'OR').')';
    return $query->whereRaw($where_raw,[]);
  }

  static public function entry($form){
    $ret = [];
    $_nos = UserTag::where('tag_key', 'teacher_no')->get();
    $_no = 0;
    foreach($_nos as $__no){
      $__no = $__no['tag_value'];
      $__no = intval(ltrim($__no, '0'));
      if($_no < $__no) $_no = $__no;
    }
    $teacher_no = $_no+1;

    $user = User::create([
        'name' => $form['name_last'].' '.$form['name_first'],
        'email' => $form['email'],
        'image_id' => 3,
        'status' => 1,
        'access_key' => $form['access_key'],
        'password' => '-',
    ]);
    $teacher = Teacher::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => '',
      'kana_first' => '',
      'status' => 'trial',
      'user_id' => $user->id,
      'create_user_id' => 1,
    ]);
    UserTag::setTag($user->id,'teacher_no',$teacher_no,$user->id);

    return $teacher;
  }
  public function profile_update($form){
    $update_field = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'birth_day' => "",
      'gender' => "",
      'phone_no' => "",
      'address' => "",
    ];
    $update_form = ['status' => 'regular'];
    foreach($update_field as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);

    $charge_subject_level_items = GeneralAttribute::findKey('charge_subject_level_item')->get();
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson', 'kids_lesson', 'english_talk_lesson', 'teacher_character',];
    $lesson_weeks = GeneralAttribute::findKey('lesson_week')->get();
    //講師用の希望シフト
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'lesson_'.$lesson_week['attribute_value'].'_time';
    }
    //事務用の希望シフト
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'work_'.$lesson_week['attribute_value'].'_time';
    }
    //体験授業シフト
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'trial_'.$lesson_week['attribute_value'].'_time';
    }

    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['piano_level', 'english_teacher', 'schedule_remark'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $this->user->update(['status' => 0]);
  }
  /*
  public function get_charge_subject(){
    //担当科目を取得
    $subjects = [];
    $tags = $this->user->tags;
    foreach($this->user->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        //補習以上可能なものを取得
        if(intval($tag->tag_value) > 1){
          $subjects[$tag->tag_key] = intval($tag->tag_value);
        }
      }
    }
    return $subjects;
  }
  public function get_enable_subjcet($lesson){
    $ret = [];
    $lesson = intval($lesson);
    if($lesson===1){
      $tags = $this->user->tags;
      foreach($this->user->tags as $tag){
        $tag_data = $tag->details();
        if(isset($tag_data['charge_subject_level_item'])){
          if(intval($tag->tag_value) > 1){
            $subject_key = str_replace('_level', '', $tag->tag_key);
            $ret[$subject_key] = [
              "subject_key" => $subject_key,
              "subject_name" => $tag->keyname(),  //科目名
              "level_name" => $tag->name(), //補習可能、受験可能など
              "style" => "secondary",
            ];
          }
        }
      }
    }
    else if($lesson===3){
      //ピアノの場合特に判断基準なし
      $ret['piano'] = [
        "subject_key" => 'piano',
        "subject_name" => 'ピアノ',  //科目名
        "level_name" => '',
        "style" => "primary",
      ];
    }
    else if($lesson==4 || $lesson==2){
      $key_name = 'kids_lesson';
      if($lesson==2){
        $key_name = 'english_talk_lesson';
      }
      foreach($this->user->tags as $tag){
        if($tag->tag_key !== $key_name) continue;
        //対応可能
        $ret[$tag->tag_value] = [
          "subject_key" => $tag->tag_value,
          "subject_name" => $tag->name(),
          "style" => "secondary",
        ];
      }
    }
    return $ret;
  }
  */
  public function is_manager(){
    $manager = Manager::where('user_id', $this->user_id)->first();
    if(isset($manager)) return true;
    return false;
  }
  public function to_manager($access_key, $already_manager_id=0){
    $_create_form =[
      'name_last' => $this->name_last,
      'name_first' => $this->name_first,
      'kana_last' => $this->kana_last,
      'kana_first' => $this->kana_first,
      'birth_day' => $this->birth_day,
      'gender' => $this->gender,
      'phone_no' => $this->phone_no,
      'address' => $this->address,
    ];
    $manager = null;
    if(isset($already_manager_id) && $already_manager_id > 0){
      $manager = Manager::where('id', $already_manager_id)->first();
      //既存マネージャーのuserを削除ステータス
      User::where('id', $manager->user_id)->update(['status' => 9]);
      //既存マネージャーのuser_idを差し替え
      $manager->update(['user_id'=>$this->user_id]);
      $manager->profile_update($_create_form);
    }
    else {
      $_create_form['user_id'] = $this->user_id;
      $manager = Manager::entry($_create_form);
      $manager->profile_update($_create_form);
    }
    $this->user->update(['status' => 1,
                          'access_key' => $access_key
                        ]);
    return $manager;
  }
  public function get_charge_students(){
    $items = [];
    foreach($this->chargeStudents as $charge_student){
      $detail = $charge_student->student->user->details("students");
      $detail['grade'] = $detail->tag_value('grade');
      $items[$detail->id] = $detail;
    }
    return $items;
  }
}
