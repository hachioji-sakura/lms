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
    $key = $this->tag_key;
    if($key==="lesson_time_holiday") $key = "lesson_time";
    $item = GeneralAttribute::where('attribute_key', $key)
      ->where('attribute_value', $this->tag_value)->first();
    if(empty($item)) return null;
    return $item;
  }
  public static function setTag($user_id, $tag_key, $tag_value , $create_user_id){
    if(!isset($this)){
      $item = UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
    }
    else {
      if(empty($tag_value)){
        $this->delete();
        return null;
      }
      else {
        $this->update([
          'tag_value' => $tag_value,
        ]);
      }
    }
    return $this;
  }
  public static function setTags($user_id, $tag_key, $tag_values, $create_user_id){
    if(isset($this)){
      $this->delete();
    }
    foreach($tag_values as $tag_value){
      $item = UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return UserTag::where('user_id', $user_id)->where('tag_key', $tag_key)->get();
  }
  public function name(){
    $item = $this->details();
    if(empty($item)) return $this->tag_value;

    return $item->attribute_name;
  }
}
