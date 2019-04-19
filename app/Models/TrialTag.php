<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialTag extends UserTag
{
  protected $table = 'trial_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'trial_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function trial(){
    return $this->belongsTo('App\Models/Trial', 'trial_id');
  }
  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($trial_id, $tag_key, $tag_value , $create_user_id){
    TrialTag::where('trial_id', $trial_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = TrialTag::create([
        'trial_id' => $trial_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($trial_id, $tag_key, $tag_values, $create_user_id){
    TrialTag::where('trial_id', $trial_id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = TrialTag::create([
        'trial_id' => $trial_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return TrialTag::where('trial_id', $trial_id)->where('tag_key', $tag_key)->get();
  }

}
