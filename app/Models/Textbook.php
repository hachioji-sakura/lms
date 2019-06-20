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
  public function chapters(){
    return $this->hasMany('App\Models\TextbookChapter');
  }
  public function image(){
    return $this->belongsTo('App\Models\Image');
  }
  public function publisher(){
    return $this->belongsTo('App\Models\Publisher');
  }
}
