<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookQuestion extends Model
{
  protected $table = 'lms.textbook_questions';
  protected $guarded = array('id');
  public static $rules = array(
      'chapter_id' => 'required',
      'sort_no' => 'required',
      'title' => 'required',
      'score' => 'required',
  );
  public function chapter(){
    return $this->belongsTo('App\Models\TextbookChapter', 'chapter_id');
  }
  public function answers(){
    return $this->hasMany('App\Models\TextbookAnswer', 'question_id');
  }
  public function user_answers(){
    return $this->hasMany('App\Models\UserAnswer', 'question_id');
  }
}
