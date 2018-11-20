<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAlias extends Model
{
  protected $table = 'user_aliases';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'alias_key' => 'required',
      'alias_value' => 'required'
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
}
