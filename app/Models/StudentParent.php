<?php

namespace App\Models;
use App\User;
use App\Models\Student;
use App\Models\UserTag;
use App\Models\StudentParent;
use App\Models\StudentRelation;

use Illuminate\Database\Eloquent\Model;
class StudentParent extends Model
{
  protected $table = 'student_parents';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
  );
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  static protected function entry($form){
    $ret = [];
    $parent_user = User::create([
        'name' => $form['name_last'].' '.$form['name_first'],
        'email' => $form['email'],
        'image_id' => 4,
        'status' => 1,
        'access_key' => $form['access_key'],
        'password' => '-',
    ]);
    $parent = StudentParent::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => '',
      'kana_first' => '',
      'user_id' => $parent_user->id,
      'create_user_id' => 1,
    ]);
    return $parent;
  }
  public function brother_add($form){
    $ret = [];
    $form['create_user_id'] = $this->user_id;
    $student = Student::entry($form);
    StudentRelation::create([
      'student_id' => $student->id,
      'student_parent_id' => $this->id,
      'create_user_id' => $this->user_id,
    ]);
    $student->profile_update($form);
    return $student;
  }
  public function profile_update($form){
    $this->update([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
    ]);
    $this->update([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
    ]);
    $tag_names = ['phone_no', 'howto_word'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['howto'];
    foreach($tag_names as $tag_name){
	    if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $this->user->update(['status'=> 0]);
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation');
  }
  public function childs(){
    $items = StudentRelation::where('student_parent_id', $this->id)->get();
    $childs = [];
    foreach($items as $item){
      $child = Student::where('id', $item->student_id)->first();
      $childs[] =$child;
    }
    return $childs;
  }
}
