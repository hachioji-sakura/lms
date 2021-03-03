<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookSubject extends Model
{
  protected $table = 'lms.textbook_subjects';
  protected $guarded = array('id');
  public static $rules = array(

  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook','textbook_id','id')->withDefault();
  }
  public function subject(){
    return $this->belongsTo('App\Models\Subject','subject_id','id')->withDefault();
  }
}
