<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
  protected $table = 'user_answers';
  protected $guarded = array('id');
  public static $rules = array(
      'question_id' => 'required',
      'user_examination_id' => 'required',
      'start_time' => 'required',
      'judge' => 'required',
      'is_traning' => 'required',
      'score' => 'required',
  );
  public function question(){
    return $this->belongsTo('App\Models\TextbookQuestion', 'question_id');
  }
  public function examination(){
    return $this->belongsTo('App\Models\UserExamination');
  }
}
