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

  public function scopeSearchWord($query, $search_words){
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        if(empty($_search_word)) continue;
        $_like = '%'.$_search_word.'%';
        $query->orWhere('name','like',$_like)->orWhere('explain','like',$_like);
      }
    });
    return $query;
  }

  public function scopeSearchSubject($query,$subjects){
    foreach($subjects as $subject){
      $query = $query->whereHas('subjects', function($q) use ($subject){
        $q->where('subject_id',$subject );
      });
    }
    return $query;
  }

  public function scopeSearchGrade($query,$grades){
    foreach($grades as $grade){
      $query = $query->whereHas('textbook_tags', function($q) use ($grade){
         $q->where('tag_value',$grade );
     });
    }
    return $query;
  }

  public function getGradeListAttribute()
  {
    $gradeList = [];
    if (isset($this->textbook_tags)) {
      $grades = $this->textbook_tags->where('tag_key', 'grade');
      if (!$grades->isEmpty()) {
        foreach ($grades as $grade) {
          $gradeList[] = config('grade')[$grade->tag_value]??'';
        }
      }
    }
    return $gradeList;
  }

  public function getPricesAttribute(){
    $priceTags = $this->textbook_tags()->where('tag_key','like','%_price')->get();
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
    $subject_names = $this->subjects()->pluck('name')->toArray();
    return $subject_names;
  }

  public function getSupplierNameAttribute(){
    return $this->supplier->name;
  }

  public function getPublisherNameAttribute(){
    return $this->publisher->name;
  }

  public function getDifficultyNameAttribute(){
    return config('attribute.difficulty')[$this->difficulty]??'-';
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
    foreach ($update_field as $key => $val) {
      if (array_key_exists($key, $form)) {
        $update_form[$key] = $form[$key];
      }
    }

    $textbook = $this->create($update_form);
    $textbook->subjects()->attach($form['subjects']);
    foreach($form['grade'] as $grade){
      $textbook->textbook_tags()
        ->create(['tag_key' => 'grade','tag_value' => $grade,'create_user_id' => $form['create_user_id']]);
    }

    $tag_names = ['teika_price', 'selling_price', 'amazon_price', 'publisher_price', 'other_price'];
    foreach ($tag_names as $tag_name) {
      if (isset($form[$tag_name]) && !empty($form[$tag_name])) {
        $textbook->textbook_tags()
          ->create(['tag_key' => $tag_name,'tag_value' => $form[$tag_name],'create_user_id' => $form['create_user_id']]);
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
    $this->subjects()->sync($form['subjects']);
    $this->textbook_tags()->delete();

    foreach($form['grade'] as $grade){
      $this->textbook_tags()
           ->create(['tag_key' => 'grade','tag_value' => $grade,'create_user_id' => $form['create_user_id']]);
    }

    $tag_names = ['teika_price', 'selling_price', 'amazon_price', 'publisher_price', 'other_price'];
    foreach ($tag_names as $tag_name) {
      if (isset($form[$tag_name]) && !empty($form[$tag_name])) {
        $this->textbook_tags()
             ->create(['tag_key' => $tag_name,'tag_value' => $form[$tag_name],'create_user_id' => $form['create_user_id']]);
      }
    }
  }

  public function dispose(){
    $this->subjects()->detach();
    $this->textbook_tags()->delete();
    $this->delete();
  }

  public function textbook_tags(){
    return $this->hasMany('App\Models\TextbookTag','textbook_id','id');
  }
  public function subjects(){
    return $this->belongsToMany('App\Models\Subject','textbook_subjects')->withTimestamps();
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
  public function tasks(){
    return $this->morphedByMany('App\Models\Task', 'textbookable')->withTimestamps();
  }
  public function schools(){
    return $this->morphedByMany('App\Models\School', 'textbookable')->withTimestamps();
  }
}
