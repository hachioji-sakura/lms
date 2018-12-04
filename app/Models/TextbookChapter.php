<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookChapter extends Model
{
  protected $table = 'textbook_chapters';
  protected $guarded = array('id');
  public static $rules = array(
      'sort_no' => 'required',
      'title' => 'required',
  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook');
  }
  public function questions(){
    return $this->hasMany('App\TextbookQuestion', 'chapter_id');
  }
}
