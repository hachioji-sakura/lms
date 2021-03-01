<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextbookChapter
 *
 * @property int $id
 * @property int $textbook_id 教科書マスタのID
 * @property int $sort_no 順番
 * @property string $title 章タイトル
 * @property string $body 章説明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TextbookQuestion[] $questions
 * @property-read \App\Models\Textbook $textbook
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookChapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookChapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookChapter query()
 * @mixin \Eloquent
 */
class TextbookChapter extends Model
{
  protected $table = 'lms.textbook_chapters';
  protected $guarded = array('id');
  public static $rules = array(
      'sort_no' => 'required',
      'title' => 'required',
  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook');
  }
  public function questions(){
    return $this->hasMany('App\Models\TextbookQuestion', 'chapter_id');
  }
}
