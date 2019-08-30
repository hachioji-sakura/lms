<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookAnswer extends Model
{
  protected $table = 'lms.textbook_answers';
  protected $guarded = array('id');
  public static $rules = array(
      'question_id' => 'required',
      'title' => 'required',
      'score' => 'required',
  );
  public function textbook_question(){
    return $this->belongsTo('App\Models\TextbookQuestion', 'question_id');
  }
}
