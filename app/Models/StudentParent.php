<?php

namespace App\Models;

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
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelations');
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
