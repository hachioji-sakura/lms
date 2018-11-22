<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTag extends Model
{
  protected $table = 'user_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
}
