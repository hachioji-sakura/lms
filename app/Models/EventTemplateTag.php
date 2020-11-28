<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTemplateTag extends UserTag
{

  protected $table = 'lms.event_template_tags';
  protected $guarded = array('id');
  public static $id_name = 'event_template_id';

  public static $rules = array(
      'event_template_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function event_template(){
    return $this->belongsTo('App\Models\EventTemplate', 'event_template_id');
  }

}
