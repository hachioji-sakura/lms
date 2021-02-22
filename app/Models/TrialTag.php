<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrialTag
 *
 * @property int $id
 * @property int $trial_id 体験申し込みID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Trial $trial
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag query()
 * @mixin \Eloquent
 */
class TrialTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.trial_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'trial_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function trial(){
    return $this->belongsTo('App\Models\Trial', 'trial_id');
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
  public static function clearTags($trial_id, $tag_key){
    TrialTag::where('trial_id', $trial_id)
      ->where('tag_key' , $tag_key)->delete();
  }

}
