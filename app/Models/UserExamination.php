<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserExamination
 *
 * @property int $id
 * @property int $parent_id リトライ元試験ID
 * @property int $user_id 回答者ID
 * @property string $chapter_id 教科書の章ID
 * @property int $status 試験状況:実施前=0/実施中=1/練習=2/完了=10
 * @property int $current_question_id 直近対応問題ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserAnswer[] $answers
 * @property-read \App\Models\TextbookChapter $textbook_chapter
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserExamination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExamination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExamination query()
 * @mixin \Eloquent
 */
class UserExamination extends Model
{
  protected $table = 'lms.user_examinations';
  protected $guarded = array('id');
  public static $rules = array(
      'chapter_id' => 'required',
      'user_id' => 'required',
  );
  public function textbook_chapter(){
    return $this->belongsTo('App\Models\TextbookChapter', 'chapter_id');
  }
  public function answers(){
    return $this->hasMany('App\Models\UserAnswer');
  }
  public function user(){
    return $this->hasOne('App\User');
  }
}
