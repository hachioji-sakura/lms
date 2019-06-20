<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  protected $table = 'lms.suppliers';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function textbook(){
    return $this->hasMany('App\Models\Textbook');
  }
}
