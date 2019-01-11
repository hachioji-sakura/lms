<?php

namespace App\Models;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $table = 'students';
  protected $guarded = array('id');

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
      'gender' => 'required',
      'birth_day' => 'required',
  );
  public function age(){
    return floor((date("Ymd") - str_replace("-", "", $this->birth_day))/10000);
  }
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  public function gender()
  {
    if($this->gender===1) return "男性";
    if($this->gender===2) return "女性";
    return "その他";
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function parents(){
    $items = StudentRelation::where('student_id', $this->id)->get();
    $parents = [];
    foreach($items as $item){
      $parent = StudentParent::where('id', $item->student_parent_id)->first();
      $parents[] =User::where('id', $parent->user_id)->first();
    }
    return $parents;
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelations');
  }
}
