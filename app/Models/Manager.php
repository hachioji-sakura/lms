<?php

namespace App\Models;
//データセット
use App\User;
use App\Models\Manager;
use App\Models\UserTag;

use Illuminate\Database\Eloquent\Model;

class Manager extends Teacher
{
  protected $connection = 'mysql_common';
  protected $table = 'common.managers';
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
        'status' => 'trial',
      ]);
    }
    UserTag::setTag($user->id,'manager_no',$manager_no,$user->id);

    return $manager;
  }
  public function is_admin(){
    if($this->user->has_tag('manager_type', 'admin')) return true;
    return false;
  }
}
