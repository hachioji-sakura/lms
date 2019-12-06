<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
  protected $connection = 'mysql_common';
  protected $table = 'common.holidays';
  protected $guarded = array('id');

  public static $rules = array(
      'date' => 'required',
  );
}
