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
    return $this->belongsTo('App\Models\Textbook','id','textbook_id');
  }
  public function subject(){
    return $this->belongsTo('App\Models\Textbook','id','subject_id');
  }
}
