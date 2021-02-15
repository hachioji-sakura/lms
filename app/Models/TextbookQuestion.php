<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextbookQuestion
 *
 * @property int $id
 * @property int $chapter_id 教科書の章ID
 * @property int $sort_no 順番
 * @property string $title 問題文
 * @property string $body 問題説明
 * @property int $score 配点
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TextbookAnswer[] $answers
 * @property-read \App\Models\TextbookChapter $chapter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserAnswer[] $user_answers
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookQuestion query()
 * @mixin \Eloquent
 */
class TextbookQuestion extends Model
{
  protected $table = 'lms.textbook_questions';
  protected $guarded = array('id');
  public static $rules = array(
      'chapter_id' => 'required',
      'sort_no' => 'required',
      'title' => 'required',
      'score' => 'required',
  );
  public function chapter(){
    return $this->belongsTo('App\Models\TextbookChapter', 'chapter_id');
  }
  public function answers(){
    return $this->hasMany('App\Models\TextbookAnswer', 'question_id');
  }
  public function user_answers(){
    return $this->hasMany('App\Models\UserAnswer', 'question_id');
  }
}
