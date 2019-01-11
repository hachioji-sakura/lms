<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralAttribute;

class UserTag extends Model
{
  protected $table = 'user_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
  public function details(){
    return GeneralAttribute::where('attribute_key', $this->tag_key)
      ->where('attribute_value', $this->tag_value)->first();
  }
  public function name(){
    return $this->details()->attribute_name;
  }
}
