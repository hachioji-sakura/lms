<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Textbook
 *
 * @property int $id
 * @property string $name
 * @property string $explain 説明
 * @property int $selling_price 販売価格
 * @property int $list_price 定価
 * @property int $price1
 * @property int $price2
 * @property int $price3
 * @property string $url 販売元ページ
 * @property int $image_id 本の写真など
 * @property int $publisher_id 出版社ID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TextbookChapter[] $chapters
 * @property-read \App\Models\Image $image
 * @property-read \App\Models\Publisher $publisher
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook query()
 * @mixin \Eloquent
 */
class Textbook extends Model
{
  protected $table = 'lms.textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required'
  );
  public function chapters(){
    return $this->hasMany('App\Models\TextbookChapter');
  }
  public function image(){
    return $this->belongsTo('App\Models\Image');
  }
  public function publisher(){
    return $this->belongsTo('App\Models\Publisher');
  }
}
