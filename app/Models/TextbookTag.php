<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextbookTag
 *
 * @property int $id
 * @property int $textbook_id 教科書ID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookTag query()
 * @mixin \Eloquent
 */
class TextbookTag extends Model
{
  protected $table = 'lms.textbook_tags';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
