<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookTag extends Model
{
  protected $table = 'lms.textbook_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
