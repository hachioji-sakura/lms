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
//
//  public function details($user_id=0){
//    //TODO deitalsにて、状態最適化ロジックが入っている問題がある↓
//    $item = $this;
//    $item['teaching_name'] = 'test';
//    return $item;
//  }

  public function textbook_update($form){
    //lms.textbooks
    $update_field = [
      'name' => "",
      'explain' => "",
      'difficulty' => "",
      'publisher_id' => "",
      'supplier_id' => "",
      'create_user_id' => "",
    ];
    $update_form = [];
    foreach($update_field as $key => $val){
      if(array_key_exists($key, $form)){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);
    TextbookSubject::clearSubjects($this->id);
    if(isset($form['subject'])) {
      TextbookSubject::setSubjects($this->id, $form['subject']);
    }

    $tag_names = ['grade_no'];
    foreach($tag_names as $tag_name){
        TextbookTag::clearTags($this->id, $tag_name);
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && count($form[$tag_name])>0){
        TextbookTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }

    $tag_names = ['teika_price','selling_price','amazon_price','publisher_price','other_price'];
    foreach($tag_names as $tag_name){
        TextbookTag::clearTags($this->id, $tag_name);
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && !empty($form[$tag_name])){
        TextbookTag::setTag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }

//
//    $tag_names = ['schedule_remark'];
//    foreach($tag_names as $tag_name){
//      if(empty($form[$tag_name])) $form[$tag_name] = '';
//      TextbookTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
//    }
//    if(!empty($form['locale'])){
//      $this->user->update(['locale' => $form['locale']]);
//    }



  }


  public function is_season_lesson(){
//    if($this->work==10 || $this->work==11) return true;
    return false;
  }

  public function is_online(){
//    if($this->work==10 || $this->work==11) return true;
    return false;
  }

  public function change($form, $file=null, $is_file_delete = false){
//    $s3_url = '';
//    $_form = $this->file_upload($file);
//    $form['s3_url'] = $_form['s3_url'];
//    $form['s3_alias'] = $_form['s3_alias'];
//    if($is_file_delete == true){
//      $form['s3_url'] = "";
//      $form['s3_alias'] = "";
//    }
//    if($is_file_delete==true){
//      //削除指示がある、もしくは、更新する場合、S3から削除
//      $this->s3_delete($this->s3_url);
//    }
    dump($form);
    $this->update($form);
    return $this;
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
