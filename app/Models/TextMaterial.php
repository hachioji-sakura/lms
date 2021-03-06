<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextMaterial extends Milestone
{
  //リンクするテーブル名
  protected $table = 'lms.text_materials';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'name' => 'required',
  );

  protected $appends = ['create_user_name', 'publiced_date', 'created_date', 'updated_date'];

  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function shared_users(){
    return $this->morphToMany('App\User', 'shared_userable');
  }
  public function curriculums(){
    return $this->morphToMany('App\Models\Curriculum', 'curriculumable');
  }
  public function getPublicedDateAttribute(){
    return $this->_date_label($this->publiced_at, 'Y年m月d日');
  }
  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name','like', $_like)
          ->orWhere('description','like', $_like);
      }
    });
    return $query;
  }
  public function scopeSearchCurriculums($query, $curriculums){
    if(!isset($curriculums)) return $query;
    if(!isset($this->curriculums)) return $query;
    return $query->whereHas('curriculums', function($query) use ($curriculums) {
      $query->whereIn('curriculum_id', $curriculums);
    });
  }
}
