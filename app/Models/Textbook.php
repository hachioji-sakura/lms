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

  public function get_grades(){
    $textbookTags = $this->textbook_tag;
    $grades = [];
    if(!$textbookTags->isEmpty()){
      foreach($textbookTags as $textbookTag){
        if($textbookTag->tag_key === 'grade_no'){
          $grades[] = GeneralAttribute::find($textbookTag->tag_value);
        }
      }
      return $grades;
    }
  }

  public function get_subjects(){
    $textbook_subjects = TextbookSubject::where('textbook_id',$this->id)->get();
    $subjects = [];
    if(isset($textbook_subjects)){
      foreach( $textbook_subjects as $textbook_subject){
        $subjects[] = $textbook_subject->subject;
      }
    }
    return $subjects;
  }


  public function get_prices(){
    $key = 'tag_key';
    $val = '_price';
    $filtered = $this->textbook_tag->filter(function ($record) use ($key, $val) {
      return strpos($record[$key], $val) !== false;
    });
    return $filtered;
  }

  public function details($user_id=0){
    $item = $this;
    return $item;
  }

  public function store_textbook($form){
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
    if(empty($form['explain'])){
      $update_form['explain'] = '';
    }
    $textbook = $this->create($update_form);

    TextbookSubject::clear_subjects($textbook->id);
    if(isset($form['subject'])) {
      TextbookSubject::set_subjects($textbook->id, $form['subject']);
    }

    $tag_names = ['grade_no'];
    foreach($tag_names as $tag_name){
      TextbookTag::clear_tags($textbook->id, $tag_name);
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && count($form[$tag_name])>0){
        TextbookTag::set_tags($textbook->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }

    $price_attributes = config('attribute.price');
    if(isset($price_attributes)) {
      foreach($price_attributes as $key => $value){
        TextbookTag::clear_tags($textbook->id, $key);
      }
      foreach($price_attributes as $key => $value){
        if(isset($form[$key]) && !empty($form[$key])){
          TextbookTag::set_tag($textbook->id, $key, $form[$key], $form['create_user_id']);
        }
      }
    }
  }

  public function update_textbook($form)
  {
    $update_field = [
      'name' => "",
      'explain' => "",
      'difficulty' => "",
      'publisher_id' => "",
      'supplier_id' => "",
      'create_user_id' => "",
    ];
    $update_form = [];
    foreach ($update_field as $key => $val) {
      if (array_key_exists($key, $form)) {
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);

    TextbookSubject::clear_subjects($this->id);
    if (isset($form['subject'])) {
      TextbookSubject::set_subjects($this->id, $form['subject']);
    }

    $tag_names = ['grade_no'];
    foreach ($tag_names as $tag_name) {
      TextbookTag::clear_tags($this->id, $tag_name);
    }
    foreach ($tag_names as $tag_name) {
      if (isset($form[$tag_name]) && count($form[$tag_name]) > 0) {
        TextbookTag::set_tags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }

    $tag_names = ['teika_price', 'selling_price', 'amazon_price', 'publisher_price', 'other_price'];
    foreach ($tag_names as $tag_name) {
      TextbookTag::clear_tags($this->id, $tag_name);
    }
    foreach ($tag_names as $tag_name) {
      if (isset($form[$tag_name]) && !empty($form[$tag_name])) {
        TextbookTag::set_tag($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
  }

  public function dispose(){
    TextbookSubject::where('textbook_id', $this->id)->delete();
    TextbookTag::where('textbook_id', $this->id)->delete();
    $this->delete();
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
