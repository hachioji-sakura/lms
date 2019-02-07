<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\UserTag;
use App\Models\GeneralAttribute;

class Teacher extends Student
{
  protected $table = 'teachers';
  protected $guarded = array('id');

  public static $rules = array(
    'name_last' => 'required',
    'name_first' => 'required',
    'kana_last' => 'required',
    'kana_first' => 'required',
  );
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

  static public function entry($form){
    $ret = [];
    $teacher_no = UserTag::where('tag_key', 'teacher_no')->max('tag_value');
    $teacher_no = intval(ltrim($teacher_no, '0'))+1;

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
      'user_id' => $user->id,
      'create_user_id' => 1,
    ]);
    UserTag::create([
      'user_id' => $user->id,
      'tag_key' => 'teacher_no',
      'tag_value' => $teacher_no,
      'create_user_id' => $user->id,
    ]);

    return $teacher;
  }
  public function profile_update($form){
    $this->update([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
      'birth_day' => $form['birth_day'],
      'gender' => $form['gender'],
      'phone_no' => $form['phone_no'],
    ]);
    $charge_subject_level_items = GeneralAttribute::findKey('charge_subject_level_item')->get();
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson'];
    $lesson_weeks = GeneralAttribute::findKey('lesson_week')->get();
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'lesson_'.$lesson_week['attribute_value'].'_time';
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
  }
}
