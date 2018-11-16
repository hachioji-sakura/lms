<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
  protected $table = 'images';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required'
  );
}
