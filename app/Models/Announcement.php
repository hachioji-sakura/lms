<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Announcement
 *
 * @property int $id
 * @property string $title 件名
 * @property string $body 内容
 * @property string $type コメント種別
 * @property string $publiced_at 公開日
 * @property int $target_user_id 対象ユーザーID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AnnouncementCheck[] $comment_checks
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
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement findDefaultTypes($domain)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement unChecked($user_id)
 * @mixin \Eloquent
 */
class Announcement extends Comment
{
  protected $table = 'lms.announcements';
  protected $guarded = array('id');

  public static $rules = array(
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    $ret = $this->attribute_name('announcement_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }
  public function scopeFindDefaultTypes($query, $domain)
  {
    $_types = config('attribute.announcement_type');
    $types = [];
    foreach($_types as $index => $val){
      $types[] = $index;
    }
    return $this->scopeFindTypes($query, $types);
  }
  public function scopeUnChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id not in (select comment_id from lms.announcement_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function comment_checks(){
    return $this->hasMany('App\Models\AnnouncementCheck');
  }
  public function is_check($user_id){
    $check = AnnouncementCheck::where('announcement_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)) return false;
    return $check->is_checked;
  }
  public function check($user_id, $val=1){
    $check = AnnouncementCheck::where('announcement_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)){
      $check = AnnouncementCheck::create([
        'announcement_id' => $this->id,
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
