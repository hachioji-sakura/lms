<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeStudentTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.charge_student_tags';
  public static $id_name = 'charge_student_id';
  protected $guarded = array('id');
  public static $rules = array(
      'charge_student_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function calendar(){
    return $this->belongsTo('App\Models\ChargeStudent', 'charge_student_id');
  }
  public function scopeFindChargeStudent($query, $val)
  {
      return $query->where('charge_student_id', $val);
  }
}
