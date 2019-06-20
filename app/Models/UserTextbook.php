<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTextbook extends Model
{
  protected $table = 'lms.user_textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'user_id' => 'required',
  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook');
  }
  public function user(){
    return $this->hasOne('App\User');
  }
}
