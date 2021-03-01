<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextMaterialTag
 *
 * @property int $id
 * @property int $text_material_id text_material_id
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TextMaterial $text_material
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterialTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterialTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterialTag query()
 * @mixin \Eloquent
 */
class TextMaterialTag extends Model
{
  //リンクするテーブル名
  protected $table = 'lms.text_material_tags';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'text_material_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function text_material(){
    return $this->belongsTo('App\Models\TextMaterial');
  }
}
