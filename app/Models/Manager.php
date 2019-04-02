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
    $_nos = UserTag::where('tag_key', 'manager_no')->get();
    $_no = 0;
    foreach($_nos as $__no){
      $__no = $__no['tag_value'];
      $__no = intval(ltrim($__no, '0'));
      if($_no < $__no) $_no = $__no;
    }
    $manager_no = $_no+1;
    $user = null;
    if(isset($form['user_id']) && $form['user_id']>0){
      $user = User::where('id', $form['user_id'])->first();
    }
    if(!isset($user)){
      $user = User::create([
          'name' => $form['name_last'].' '.$form['name_first'],
          'email' => $form['email'],
          'image_id' => 4,
          'status' => 1,
          'access_key' => $form['access_key'],
          'password' => '-',
      ]);
    }
    $manager = Manager::where('user_id', $user->id)->first();
    if(!isset($manager)){
      $manager = Manager::create([
        'name_last' => $form['name_last'],
        'name_first' => $form['name_first'],
        'kana_last' => '',
        'kana_first' => '',
        'user_id' => $user->id,
        'create_user_id' => $user->id,
      ]);
    }
    UserTag::setTag($user->id,'manager_no',$manager_no,$user->id);

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
