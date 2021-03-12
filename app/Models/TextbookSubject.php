<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookSubject extends Model
{
  protected $table = 'lms.textbook_subjects';
  protected $guarded = array('id');
  public static $rules = array(

  );
  public static function clearSubjects($textbook_id){
    TextbookSubject::where('textbook_id', $textbook_id)
      ->delete();
  }

  public static function setSubjects($textbook_id,$subjects){
    TextbookSubject::where('textbook_id',$textbook_id)
      ->delete();
    foreach($subjects as $subject)
    $item = TextbookSubject::create([
      'textbook_id' => $textbook_id,
      'subject_id' => $subject,
    ]);
    return $item;
  }

  public function textbook(){
    return $this->belongsTo('App\Models\Textbook','textbook_id','id')->withDefault();
  }
  public function subject(){
    return $this->belongsTo('App\Models\Subject','subject_id','id')->withDefault();
  }
}
