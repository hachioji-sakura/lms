<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommentCheck
 *
 * @property int $id
 * @property int $comment_id コメントID
 * @property int $check_user_id チェックしたユーザー
 * @property int $is_checked チェックした=1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $check_user
 * @property-read \App\Models\Comment $comment
 * @method static \Illuminate\Database\Eloquent\Builder|CommentCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentCheck query()
 * @mixin \Eloquent
 */
class CommentCheck extends Model
{
  protected $table = 'lms.comment_checks';
  protected $guarded = array('id');
  public static $rules = array(
      'comment_id' => 'required',
      'check_user_id' => 'required',
  );

  public function check_user(){
    return $this->belongsTo('App\User', 'check_user_id');
  }
  public function comment(){
    return $this->belongsTo('App\Models\Comment', 'comment_id');
  }

}
