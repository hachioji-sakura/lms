<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
  protected $table = 'publishers';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function textbook(){
    return $this->hasMany('App\Models\Textbook');
  }
}
