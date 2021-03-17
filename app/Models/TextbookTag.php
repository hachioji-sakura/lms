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
  public function scopePrices($query)
  {
    return $query->where('tag_key','LIKE' ,'_price');
  }

  //1 key = 1 tagの場合利用する(上書き差し替え）
  public static function set_tag($textbook_id, $tag_key, $tag_value , $create_user_id){
    TextbookTag::where('textbook_id', $textbook_id)
      ->where('tag_key' , $tag_key)->delete();
    $item = TextbookTag::create([
      'textbook_id' => $textbook_id,
      'tag_key' => $tag_key,
      'tag_value' => $tag_value,
      'create_user_id' => $create_user_id,
    ]);
    return $item;
  }
  //1 key = n tagの場合利用する(上書き差し替え）
  public static function set_tags($textbook_id, $tag_key, $tag_values, $create_user_id){
    TextbookTag::where('textbook_id', $textbook_id)
      ->where('tag_key' , $tag_key)->delete();
    foreach($tag_values as $tag_value){
      $item = TextbookTag::create([
        'textbook_id' => $textbook_id,
        'tag_key' => $tag_key,
        'tag_value' => $tag_value,
        'create_user_id' => $create_user_id,
      ]);
    }
    return TextbookTag::where('textbook_id', $textbook_id)->where('tag_key', $tag_key)->get();
  }
  public static function clear_tags($textbook_id, $tag_key){
    TextbookTag::where('textbook_id', $textbook_id)
      ->where('tag_key' , $tag_key)->delete();
  }




  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function textbook(){
    return $this->belongsTo('App\Textbook','textbook_id','id' );
  }
}
