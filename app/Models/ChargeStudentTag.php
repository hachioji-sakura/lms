<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChargeStudentTag
 *
 * @property int $id
 * @property int $charge_student_id 担当生徒ID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChargeStudent $calendar
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag findChargeStudent($val)
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag query()
 * @mixin \Eloquent
 */
class ChargeStudentTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.charge_student_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'charge_student_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function calendar(){
    return $this->belongsTo('App\Models\ChargeStudent', 'charge_student_id');
  }
  public function scopeFindChargeStudent($query, $val)
  {
      return $query->where('charge_student_id', $val);
  }
  public function scopeFindKey($query, $val)
  {
      return $query->where('tag_key', $val);
  }

  //1 key = 1tagの場合利用する(上書き差し替え）
  public static function setTag($charge_student_id, $tag_key, $tag_value , $create_user_id){
    ChargeStudentTag::where('charge_student_id', $charge_student_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = ChargeStudentTag::create([
        'charge_student_id' => $charge_student_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
      return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function setTags($charge_student_id, $tag_key, $tag_values, $create_user_id){
    ChargeStudentTag::where('charge_student_id', $charge_student_id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = ChargeStudentTag::create([
        'charge_student_id' => $charge_student_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return ChargeStudentTag::where('charge_student_id', $charge_student_id)->where('tag_key', $tag_key)->get();
  }
}
