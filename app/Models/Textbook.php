<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
  protected $table = 'lms.textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required'
  );

  public function getGrade(){
    $textbookTags = $this->textbook_tag;
    $grades = '';
    if($textbookTags->isEmpty()){
      return '';
    }else {
     foreach($textbookTags as $textbookTag){
       if($textbookTag->tag_key === 'grade_no'){
         $grade = GeneralAttribute::find($textbookTag->tag_value);
         $grades = $grades.$grade->attribute_name.',';
       }
     }
      return $grades;
    }
  }

  public function getSubjectName(){
    $subjects = '';
    foreach($this->textbook_subject as $textbookSubject){
      if(isset($textbookSubject->subject->name)) {
        $subjects = $subjects . $textbookSubject->subject->name . ',';
      }
    }
    if($subjects !== ''){
      return mb_substr($subjects, 0, -1);
    }
    return $subjects;
  }

  public function textbook_tag(){
    return $this->hasMany('App\Models\TextbookTag');
  }
  public function textbook_subject(){
    return $this->hasMany('App\Models\TextbookTag','textbook_id','id');
  }
  public function publisher(){
    return $this->belongsTo('App\Models\Publisher','id','publisher_id');
  }
  public function chapters(){
    return $this->hasMany('App\Models\TextbookChapter');
  }
  public function image(){
    return $this->belongsTo('App\Models\Image');
  }

}
