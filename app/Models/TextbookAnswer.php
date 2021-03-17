<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextbookAnswer
 *
 * @property int $id
 * @property int $question_id 問題ID
 * @property int $sort_no 順番
 * @property string $answer_text 回答
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TextbookQuestion $textbook_question
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookAnswer query()
 * @mixin \Eloquent
 */
class TextbookAnswer extends Model
{
  protected $table = 'lms.textbook_answers';
  protected $guarded = array('id');
  public static $rules = array(
      'question_id' => 'required',
      'title' => 'required',
      'score' => 'required',
  );
  public function textbook_question(){
    return $this->belongsTo('App\Models\TextbookQuestion', 'question_id');
  }
}
