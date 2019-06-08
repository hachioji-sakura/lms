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
  public function scopeMydata($query, $val)
  {
      return $query->where('target_user_id', $val);
  }
  public function scopeStatus($query, $val)
  {
      return $query->where('status', $val);
  }
  public function scopeFindTypes($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'type', $vals, $is_not);
  }
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
    if(count($vals) > 0){
      if($is_not===true){
        $query = $query->whereNotIn($field, $vals);
      }
      else {
        $query = $query->whereIn($field, $vals);
      }
    }
    return $query;
  }
  public function scopePagenation($query, $page, $line){
    $_line = $this->pagenation_line;
    if(is_numeric($line)){
      $_line = $line;
    }
    $_page = 0;
    if(is_numeric($page)){
      $_page = $page;
    }
    $_offset = $_page*$_line;
    if($_offset < 0) $_offset = 0;
    return $query->offset($_offset)->limit($_line);
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
  protected function config_attribute_name($key, $value){
    $_lists = config('attribute.'.$key);
    return $_lists[$value];
  }
  public function details(){
    $item = $this;
    $item["type_name"] = $this->type_name();
    $item["create_user_name"] = $this->create_user->details()->name();
    $item["target_user_name"] = $this->target_user->details()->name();
    return $item;
  }
}
