<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralAttribute  extends Model
{
  protected $table = 'general_attributes';
  protected $guarded = array('id');

  public static $rules = array(
      'attribute_key' => 'required',
      'attribute_value' => 'required',
      'attribute_name' => 'required'
  );

  public function scopeFindVal($query, $val)
  {
      return $query->where('attribute_value', $val);
  }
  public function scopeLesson($query, $val)
  {
      return $query->where('attribute_key', 'lesson')->findVal($val);
  }
  public function scopeSubject($query, $val)
  {
      return $query->where('attribute_key', 'subject')->findVal($val);
  }
  public function scopeCourse($query, $val)
  {
      return $query->where('attribute_key', 'course')->findVal($val);
  }
}
