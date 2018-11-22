<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookTag extends Model
{
  protected $table = 'textbook_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
}
