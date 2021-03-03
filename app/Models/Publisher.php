<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
  protected $table = 'lms.publishers';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function textbook(){
    return $this->hasMany('App\Models\Textbook','id', 'publisher_id');
  }
}
