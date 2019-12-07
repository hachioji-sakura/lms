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
  public function is_public_holiday(){
    if($this->is_public_holiday==1){
      return true;
    }
    return false;
  }
  public function is_private_holiday(){
    if($this->is_private_holiday==1){
      return true;
    }
    return false;
  }
}
