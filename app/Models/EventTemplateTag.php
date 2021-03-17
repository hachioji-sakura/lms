<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EventTemplateTag
 *
 * @property int $id
 * @property int $event_template_id イベントテンプレートID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EventTemplate $event_template
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag query()
 * @mixin \Eloquent
 */
class EventTemplateTag extends UserTag
{

  protected $table = 'lms.event_template_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'event_template_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function event_template(){
    return $this->belongsTo('App\Models\EventTemplate', 'event_template_id');
  }
  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($event_template_id, $tag_key, $tag_value , $create_user_id){
    EventTemplateTag::where('event_template_id', $event_template_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = EventTemplateTag::create([
        'event_template_id' => $event_template_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($event_template_id, $tag_key, $tag_values, $create_user_id){

    EventTemplateTag::where('event_template_id', $event_template_id)
      ->where('tag_key' , $tag_key)->delete();
    if(gettype($tag_values) == 'array'){
      foreach($tag_values as $tag_value){
        $item = EventTemplateTag::create([
          'event_template_id' => $event_template_id,
          'tag_key' => $tag_key,
          'tag_value' => $tag_value,
          'create_user_id' => $create_user_id,
        ]);
      }
    }
    else {
      $item = EventTemplateTag::create([
        'event_template_id' => $event_template_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_values,
        'create_user_id' => $create_user_id,
      ]);
    }
    return EventTemplateTag::where('event_template_id', $event_template_id)->where('tag_key', $tag_key)->get();
  }
  public static function clearTags($event_template_id, $tag_key){
    EventTemplateTag::where('event_template_id', $event_template_id)
      ->where('tag_key' , $tag_key)->delete();
  }

}
