<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.trial_tags';
  public static $id_name = 'trial_id';
  protected $guarded = array('id');
  public static $rules = array(
      'trial_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function trial(){
    return $this->belongsTo('App\Models/Trial', 'trial_id');
  }



}
