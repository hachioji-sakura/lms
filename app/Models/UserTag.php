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
  public function keyname(){
    $key = $this->tag_key;
    if($key==="teacher_no") return "No";
    if($key==="student_no") return "No";
    if($key==="howto_word") return "検索時のキーワード";

    if($key==="lesson_time_holiday") $key = "lesson_time";
    $item = GeneralAttribute::where('attribute_key', 'keys')
    ->where('attribute_value', $key)->first();

    if(!empty($item)) return $item->attribute_name;

    $item = $this->details();
    if(isset($item->charge_subject_level_item)){
      return $item->charge_subject_level_item->attribute_name;
    }

    return $this->tag_key;
  }
  public function details(){
    $key = $this->tag_key;

    //if($key==="lesson_time_holiday") $key = "lesson_time";

    $item = GeneralAttribute::where('attribute_key', $this->tag_key)
      ->where('attribute_value', $this->tag_value)->first();

    //general_attributeのattribute_keyをtag_keyとして使っている場合
    if(!empty($item)) return $item;

    //general_attributesから取得できなかった場合
    $charge_subject_level_item = GeneralAttribute::where('attribute_key','charge_subject_level_item')
        ->where('attribute_value', $this->tag_key)->first();

    if(!empty($charge_subject_level_item)){
      //希望科目の場合
      //受験希望、補習希望　生徒向けの定義
      $key = 'lesson_subject_level';
      if($this->table === 'user_tags'){
        //ユーザータグ＝人につくので、charge_subject_level
        //受験可、補習可　講師向けの定義
        $key = 'charge_subject_level';
      }
      $item = GeneralAttribute::where('attribute_key', $key)
          ->where('attribute_value', $this->tag_value)->first();
      $item['charge_subject_level_item'] = $charge_subject_level_item;
    }
    if(!empty($item)){
      return $item;
    }
    return null;
  }
  public function scopeFindUser($query, $val)
  {
      return $query->where('user_id', $val);
  }
  public function scopeFindKey($query, $val)
  {
      return $query->where('tag_key', $val);
  }
  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($user_id, $tag_key, $tag_value , $create_user_id){
    UserTag::where('user_id', $user_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($user_id, $tag_key, $tag_values, $create_user_id){
    UserTag::where('user_id', $user_id)
      ->where('tag_key' , $tag_key)->delete();
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
    if(!isset($item) || empty($item)) return $this->tag_value;
    return $item->attribute_name;
  }
}
