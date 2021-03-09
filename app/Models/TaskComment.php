<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskComment
 *
 * @property int $id
 * @property int $task_id
 * @property string|null $title
 * @property string $body
 * @property string $type
 * @property string|null $s3_alias
 * @property string|null $s3_url
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
 * @property-read \App\Models\Task $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Comment checked($user_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment findDefaultTypes($domain)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment unChecked($user_id)
 * @mixin \Eloquent
 */
class TaskComment extends Comment
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.task_comments';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
        'type' => 'requered',
        'target_user_id' => 'required',
        'create_user_id' => 'required',
    );

    public function tasks(){
      return $this->belongsTo('App\Models\Task','task_id');
    }

    public function details(){
     $item = $this;
     $item["type_name"] = $this->type_name();
     $item["created_date"] = $this->created_at_label();
     $item["updated_date"] = $this->updated_at_label();
     $item["create_user_name"] = $this->create_user->details()->name();
     return $item;
   }
}
