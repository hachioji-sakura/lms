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
