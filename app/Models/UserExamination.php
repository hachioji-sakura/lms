<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamination extends Model
{
  protected $table = 'user_examinations';
  protected $guarded = array('id');
  public static $rules = array(
      'chapter_id' => 'required',
      'user_id' => 'required',
  );
  public function textbook_chapter(){
    return $this->belongsTo('App\Models\TextbookChapter', 'chapter_id');
  }
  public function answers(){
    return $this->hasMany('App\Models\UserAnswer');
  }
  public function user(){
    return $this->hasOne('App\User');
  }
}
