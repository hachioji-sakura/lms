<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $parent_message_id
 * @property string $title 件名
 * @property string $body 内容
 * @property string $type
 * @property string|null $s3_url
 * @property string|null $s3_alias
 * @property int $target_user_id
 * @property int $create_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommentCheck[] $comment_checks
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
 * @method static \Illuminate\Database\Eloquent\Builder|Message findMyMessage($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Message findRootMessage($parent_message_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment unChecked($user_id)
 * @mixin \Eloquent
 */
class Message extends Comment
{
    //
    protected $table = 'lms.messages';
    protected $guarded = array('id');

    public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'target_user' => 'required',
      'create_user' => 'required',
    );

    public function scopeFindRootMessage($query,$parent_message_id){
      return $query->where('parent_message_id',0)
                  ->where('id', $parent_message_id);
    }

    public function scopeFindMyMessage($query, $id){
      return $query->where('target_user_id', $id)
                   ->orWhere('create_user_id',$id);
    }

}
