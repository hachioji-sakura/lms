<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralAttribute;

/**
 * App\Models\UserTag
 *
 * @property int $id
 * @property int $user_id ユーザーID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag query()
 * @mixin \Eloquent
 */
class UserTag extends Model
{
  protected $table = 'common.user_tags';
  public static $id_name = 'user_id';
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
    if($key==="entry_milestone_word") return "やって欲しいこと（その他）";
    if($key==="howto_word") return "検索時のキーワード";

    if($key==="lesson_time_holiday") $key = "lesson_time";
    $item = GeneralAttribute::get_item('keys', $key);

    if(!empty($item)) return $item["attribute_name"];

    $item = $this->details();
    /*
    if(isset($item->charge_subject_level_item)){
      return $item->charge_subject_level_item["attribute_name"];
    }
    */

    $item = GeneralAttribute::get_item('charge_subject_level_item', $key);
    if(!empty($item)) return $item["attribute_name"];
    return $this->tag_key;
  }
  public function details(){
    $key = $this->tag_key;
    if(!isset($key)) return null;
    if($key==="kids_lesson_course_type") $key = "course_type";
    if($key==="english_talk_course_type") $key = "course_type";
    if($key==="lesson_place"){
      $place = Place::where('id', $this->tag_value)->first();
      if(isset($place)){
        $place["attribute_name"] = $place->name();
        return $place;
      }
      return null;
    }
    $item = GeneralAttribute::get_item($key, $this->tag_value);

    //general_attributeのattribute_keyをtag_keyとして使っている場合
    if($item!=null) return $item;

    //general_attributesから取得できなかった場合
    $charge_subject_level_item = GeneralAttribute::get_item('charge_subject_level_item', $this->tag_key);

    if($charge_subject_level_item!=null){
      //希望科目の場合
      //受験希望、補習希望　生徒向けの定義
      $key = 'lesson_subject_level';
      if($this->table === 'common.user_tags'){
        //ユーザータグ＝人につくので、charge_subject_level
        //受験可、補習可　講師向けの定義
        $key = 'charge_subject_level';
      }
      $item = GeneralAttribute::get_item($key, $this->tag_value);

      $item['charge_subject_level_item'] = $charge_subject_level_item;
    }

    return $item;
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
  public static function setTag($id, $tag_key, $tag_value , $create_user_id){
    static::where(static::$id_name, $id)
      ->where('tag_key' , $tag_key)->delete();
    $item = static::create([
        static::$id_name => $id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }

  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($id, $tag_key, $tag_values, $create_user_id){
    static::where(static::$id_name, $id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = static::create([
        static::$id_name => $id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return static::where(static::$id_name, $id)->where('tag_key', $tag_key)->get();
  }
  public static function clearTags($id, $tag_key){
    static::where(static::$id_name, $id)
      ->where('tag_key' , $tag_key)->delete();
  }

  public function name(){
    $item = $this->details();
    if(!isset($item) || empty($item)) return $this->tag_value;
    if(isset($item["attribute_name"])){
      return $item["attribute_name"];
    }
    return "";
  }

}
