<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
  protected $table = 'textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required'
  );
  public function chapters(){
    return $this->hasMany('App\Models\TextbookChapter');
  }
}
