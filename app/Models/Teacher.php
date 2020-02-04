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
      'bank_no' => "",
      'bank_branch_no' => "",
      'bank_account_type' => "",
      'bank_account_no' => "",
      'bank_account_name' => "",
    ];
    $update_form = ['status' => 'regular'];
    foreach($update_field as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);

    $charge_subject_level_items = GeneralAttribute::get_items('charge_subject_level_item');
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson', 'kids_lesson', 'english_talk_lesson', 'teacher_character', 'manager_type'];

    //講師用の希望シフト
    $lesson_weeks = config('attribute.lesson_week');
    foreach($lesson_weeks as $lesson_week=>$name){
      $tag_names[] = 'lesson_'.$lesson_week.'_time';
    }
    //事務用の希望シフト
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'work_'.$lesson_week.'_time';
    }
    //体験授業シフト
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'trial_'.$lesson_week.'_time';
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
    $tag_names = ['schedule_remark'];
    foreach($tag_names as $tag_name){
      if(empty($form[$tag_name])) $form[$tag_name] = '';
      UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
    }
    $this->user->update(['status' => 0]);
  }
  public function is_manager(){
    $manager = Manager::where('user_id', $this->user_id)->first();
    if(isset($manager)) return true;
    return false;
  }
  public function to_manager($access_key, $already_manager_id=0, $create_user_id){
    $_create_form =[
      'name_last' => $this->name_last,
      'name_first' => $this->name_first,
      'kana_last' => $this->kana_last,
      'kana_first' => $this->kana_first,
      'birth_day' => $this->birth_day,
      'gender' => $this->gender,
      'phone_no' => $this->phone_no,
      'address' => $this->address,
      'create_user_id' => $create_user_id,
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
  public function regular(){
    $this->user->update(['status' => 0]);
    $this->update(['status' => 'regular']);
    return $this;
  }
}
