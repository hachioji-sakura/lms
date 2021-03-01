<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserAnswer
 *
 * @property int $id
 * @property int $user_examination_id 試験ID
 * @property int $question_id 問題ID
 * @property string $answer_text 回答
 * @property int $judge 回答判定：1=正解、-1=不正解
 * @property int $is_traning 練習での回答=1 / それ以外=0
 * @property int $score 得点
 * @property string $start_time 開始時刻
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserExamination $examination
 * @property-read \App\Models\TextbookQuestion $question
 * @method static \Illuminate\Database\Eloquent\Builder|UserAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAnswer query()
 * @mixin \Eloquent
 */
class UserAnswer extends Model
{
  protected $table = 'lms.user_answers';
  protected $guarded = array('id');
  public static $rules = array(
      'question_id' => 'required',
      'user_examination_id' => 'required',
      'start_time' => 'required',
      'judge' => 'required',
      'is_traning' => 'required',
      'score' => 'required',
  );
  public function question(){
    return $this->belongsTo('App\Models\TextbookQuestion', 'question_id');
  }
  public function examination(){
    return $this->belongsTo('App\Models\UserExamination');
  }
}
