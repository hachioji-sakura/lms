<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCalendarTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.user_calendar_tags';
  public static $id_name = 'calendar_id';
  protected $guarded = array('id');
  public static $rules = array(
      'calendar_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function scopeFindCalendar($query, $val)
  {
      return $query->where('calendar_id', $val);
  }
  public function calendar(){
    return $this->belongsTo('App\Models\UserCalendar', 'calendar_id');
  }


}
