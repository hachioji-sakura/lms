<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Textbook
 *
 * @property int $id
 * @property string $name
 * @property string $explain 説明
 * @property int $selling_price 販売価格
 * @property int $list_price 定価
 * @property int $price1
 * @property int $price2
 * @property int $price3
 * @property string $url 販売元ページ
 * @property int $image_id 本の写真など
 * @property int $publisher_id 出版社ID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TextbookChapter[] $chapters
 * @property-read \App\Models\Image $image
 * @property-read \App\Models\Publisher $publisher
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook query()
 * @mixin \Eloquent
 */
class Textbook extends Model
{
  protected $table = 'lms.textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required'
  );

  public function getGradeListAttribute()
  {
    $gradeList = [];
    if (isset($this->textbook_tag)) {
      $grades = $this->textbook_tag->where('tag_key', 'grade_no');
      if (!$grades->isEmpty()) {
        foreach ($grades as $grade) {
          $generalAttribute = GeneralAttribute::find($grade->tag_value);
          $gradeList[] = $generalAttribute->attribute_name;
        }
      }
    }
    return $gradeList;
  }


  public function getPricesAttribute(){
    $priceTags = $this->textbook_tag()->where('tag_key','like','%_price')->get();
    $prices = [];
    if(!$priceTags->isEmpty()) {
      foreach ($priceTags as $priceTag) {
        $prices[$priceTag->tag_key] = $priceTag->tag_value;
      }
    }
    return $prices;
  }

  public function getSubjectListAttribute(){
    $subject_names = [];
    $subject_names = $this->subjects->pluck('name')->toArray();
    return $subject_names;
  }

  public function getSupplierNameAttribute(){
    return $this->supplier->name;
  }

  public function getPublisherNameAttribute(){
    return $this->publisher->name;
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
  public function subjects(){
    return $this->belongsToMany('App\Models\Subject','textbook_subjects');
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
