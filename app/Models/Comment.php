<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommentCheck;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property string $title 件名
 * @property string $body 内容
 * @property string|null $s3_alias アップロードファイル エイリアス
 * @property string|null $s3_url アップロードファイル　URL
 * @property string $type コメント種別
 * @property string $status ステータス
 * @property int $importance 重要度
 * @property string $publiced_at 公開日
 * @property int $target_user_id 対象ユーザーID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|CommentCheck[] $comment_checks
 * @property-read \App\User $create_user
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $importance_label
 * @property-read mixed $publiced_date
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Comment checked($user_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment findDefaultTypes($domain)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment unChecked($user_id)
 * @mixin \Eloquent
 */
class Comment extends Milestone
{
  protected $table = 'lms.comments';
  protected $guarded = array('id');


  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  protected $appends = ['type_name', 'create_user_name', 'target_user_name', 'importance_label', 'publiced_date', 'created_date', 'updated_date'];

  public function getTypeNameAttribute(){
    $ret = $this->attribute_name('comment_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }

  public function type_name()
  {
    $ret = $this->attribute_name('comment_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }
  public function publiced_date(){
    return $this->_date_label($this->publiced_at, 'Y年m月d日');
  }
  public function getPublicedDateAttribute(){
    return $this->_date_label($this->publiced_at);
  }

  public function scopeFindDefaultTypes($query, $domain)
  {
    $_types = config('attribute.comment_type');
    $types = [];
    foreach($_types as $index => $val){
      $types[] = $index;
    }
    $types[] = 'trial';
    $types[] = 'entry';
    return $this->scopeFindTypes($query, $types);
  }
  public function scopeChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id in (select comment_id from lms.comment_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }
  public function scopeUnChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id not in (select comment_id from lms.comment_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function scopeMemo($query){
    return $query->findTypes(['memo']);
  }


  public function comment_checks(){
    return $this->hasMany('App\Models\CommentCheck');
  }
  public function is_check($user_id){
    $check = CommentCheck::where('comment_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)) return false;
    return $check->is_checked;
  }
  public function check($user_id, $val=1){
    $check = CommentCheck::where('comment_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)){
      $check = CommentCheck::create([
        'comment_id' => $this->id,
        'check_user_id' => $user_id,
        'is_checked' => $val
      ]);
    }
    else {
      $check->update(['is_checked' => $val]);
    }
    return $check;
  }
  public function uncheck($user_id){
    return $this->check($user_id, 0);
  }
}
