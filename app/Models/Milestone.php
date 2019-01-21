<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralAttribute;
class Milestone extends Model
{
  protected $table = 'milestones';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    return $this->attribute_name('milestone_type', $this->type);
  }
  public function scopeStatus($query, $val)
  {
      return $query->where('status', $val);
  }
  public function target_user(){
    return $this->belongsTo('App\User', 'target_user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  protected function attribute_name($key, $value){
    $_attribute = GeneralAttribute::where('attribute_key', $key)->where('attribute_value', $value)->first();
    return $_attribute['attribute_name'];
  }
}
