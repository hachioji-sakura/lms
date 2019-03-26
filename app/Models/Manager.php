<?php

namespace App\Models;
use App\User;
use App\Models\Manager;
use App\Models\UserTag;

use Illuminate\Database\Eloquent\Model;

class Manager extends Teacher
{
  protected $table = 'managers';
  public function scopeFindChargeStudent($query, $id)
  {
    return $query;
  }
  static public function entry($form){
    $ret = [];
    $manager_no = UserTag::where('tag_key', 'manager_no')->max('tag_value');
    $manager_no = intval(ltrim($manager_no, '0'))+1;

    $user = User::create([
        'name' => $form['name_last'].' '.$form['name_first'],
        'email' => $form['email'],
        'image_id' => 4,
        'status' => 1,
        'access_key' => $form['access_key'],
        'password' => '-',
    ]);
    $manager = Manager::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => '',
      'kana_first' => '',
      'user_id' => $user->id,
      'create_user_id' => 1,
    ]);
    UserTag::create([
      'user_id' => $user->id,
      'tag_key' => 'manager_no',
      'tag_value' => $manager_no,
      'create_user_id' => $user->id,
    ]);

    return $manager;
  }
  /*
  TODO: 現状だと、teachersと同じフィールドを持ち、
  担当科目定義は、入力フォームがなければユーザータグには追加されない
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
  */
}
