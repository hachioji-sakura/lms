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

  public function getGradeAttributes(){
    $textbookTags = $this->textbook_tag;
    $grades = [];
    if($textbookTags->isEmpty()){
      return '';
    }else {
      foreach($textbookTags as $textbookTag){
        if($textbookTag->tag_key === 'grade_no'){
          $grades[] = GeneralAttribute::find($textbookTag->tag_value);

        }
      }
      return $grades;
    }
  }

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
  //todo MVC的に
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

  public function getPrices(){
    $key = 'tag_key';
    $val = '_price';
    $filtered = $this->textbook_tag->filter(function ($record) use ($key, $val) {
      return strpos($record[$key], $val) !== false;
    });
    return $filtered;
  }

  public function details($user_id=0){
    //TODO deitalsにて、状態最適化ロジックが入っている問題がある↓
    $item = $this;
    $item['teaching_name'] = 'test';


    return $item;
  }
  public function is_season_lesson(){
//    if($this->work==10 || $this->work==11) return true;
    return false;
  }

  public function is_online(){
//    if($this->work==10 || $this->work==11) return true;
    return false;
  }

  public function textbook_tag(){
    return $this->hasMany('App\Models\TextbookTag','textbook_id','id');
  }
  public function textbook_subject(){
    return $this->hasMany('App\Models\TextbookSubject','textbook_id','id');
  }
  public function publisher(){
    return $this->belongsTo('App\Models\Publisher','publisher_id','id')->withDefault();
  }
  public function supplier(){
    return $this->belongsTo('App\Models\Supplier','supplier_id','id')->withDefault();
  }
  public function chapters(){
    return $this->hasMany('App\Models\TextbookChapter');
  }
  public function image(){
    return $this->belongsTo('App\Models\Image');
  }

}
