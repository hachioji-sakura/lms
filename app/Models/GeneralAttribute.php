<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GeneralAttribute
 *
 * @property int $id
 * @property string $attribute_key 属性キー
 * @property string $attribute_value 属性値
 * @property string $attribute_name 属性名
 * @property int $sort_no 並び順
 * @property string|null $parent_attribute_key 親属性キー
 * @property string|null $parent_attribute_value 親属性値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute course($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute findId($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute findKeyValue($key, $val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute findVal($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute lesson($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute subject($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute week($val)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralAttribute work($val)
 * @mixin \Eloquent
 */
class GeneralAttribute  extends Model
{
  protected $table = 'common.general_attributes';
  protected $guarded = array('id');

  public static $rules = array(
      'attribute_key' => 'required',
      'attribute_value' => 'required',
      'attribute_name' => 'required'
  );
  public function scopeFindId($query, $val)
  {
      return $query->where('id', $val);
  }
  public function scopeFindVal($query, $val)
  {
      return $query->where('attribute_value', $val);
  }
  public function scopeFindKey($query, $val)
  {
      return $query->where('attribute_key', $val);
  }
  public function scopeFindKeyValue($query, $key, $val)
  {
      return $query->where('attribute_key', $key)->where('attribute_value', $val);
  }
  public function scopeLesson($query, $val)
  {
      return $query->where('attribute_key', 'lesson')->findVal($val);
  }
  public function scopeWeek($query, $val)
  {
      return $query->where('attribute_key', 'lesson_week')->findVal($val);
  }
  public function scopeSubject($query, $val)
  {
      return $query->where('attribute_key', 'charge_subject')->findVal($val);
  }
  public function scopeCourse($query, $val)
  {
      return $query->where('attribute_key', 'course')->findVal($val);
  }
  public function scopeWork($query, $val)
  {
      return $query->where('attribute_key', 'work')->findVal($val);
  }
  public function get_parent(){
    return GeneralAttribute::where('attribute_key', $this->parent_attribute_key)
      ->where('attribute_value', $this->parent_attribute_value)
      ->first();
  }
  static public function get_item($key, $value){
    $item = null;

    $d = config('attributes');
    if(isset($d[$key]) && isset($d[$key][$value.""])){
      $item = $d[$key][$value.""];
      $g = new GeneralAttribute;
      foreach($item as $field => $val){
        $g[$field] = $item[$field];
      }
      $item = $g;
    }
    if($item == null){
      \Log::info("------------------");
      $item = GeneralAttribute::where('attribute_key', $key)
        ->where('attribute_value', $value)->first();
        \Log::info("------------------");
    }
    return $item;
  }
  static public function get_items($key){
    $items = null;
    $d = config('attributes');
    if(isset($d[$key])){
      $items = $d[$key];
      foreach($items as $key => $item){
        $g = new GeneralAttribute;
        foreach($item as $field => $val){
          $g[$field] = $item[$field];
        }
        $items[$key] = $g;
      }
    }
    if($item == null){
      $items = GeneralAttribute::where('attribute_key', $key)->get();
    }
    return $items;
  }
  static public function get_temporary_attribute(){
    $url = '../storage/temporary/attributes.json';
    try {
        $contents = \File::get($url);
        if(!empty($contents)){
          return json_decode($contents);
        }
        return null;
    } catch (\Illuminate\Filesystem\FileNotFoundException $exception) {
      return null;
    }
    return null;
  }

}
