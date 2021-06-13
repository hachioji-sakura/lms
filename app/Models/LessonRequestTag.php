<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonRequestTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.lesson_request_tags';
  public static $id_name = 'lesson_request_id';

  protected $guarded = array('id');
  public static $rules = array(
      'lesson_request_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function lesson_request(){
    return $this->belongsTo('App\Models/LessonRequest', 'lesson_request_id');
  }

}
