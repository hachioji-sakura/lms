<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaskReview;
use DB;
use App\Models\Traits\Scopes;
use App\Models\Traits\Common;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property string $title 概要
 * @property string|null $body
 * @property int|null $milestone_id
 * @property string $type
 * @property string $status
 * @property int $target_user_id
 * @property int $create_user_id
 * @property int|null $stars
 * @property string|null $priority
 * @property string|null $s3_url
 * @property string|null $s3_alias
 * @property string|null $start_schedule
 * @property string|null $start_date
 * @property string|null $end_schedule
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Curriculum[] $curriculums
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $full_title
 * @property-read mixed $importance_label
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\Models\Milestone|null $milestones
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskComment[] $task_comments
 * @property-read \Illuminate\Database\Eloquent\Collection|TaskReview[] $task_reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Task activeTasks($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Task reviewEvaluation($evaluation)
 * @method static \Illuminate\Database\Eloquent\Builder|Task search($request)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
class Task extends Milestone
{
    //
    use Common;
    use Scopes;
    protected $connection = 'mysql';
    protected $table = 'lms.tasks';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'status' => 'required',
        'target_user_id' => 'required',
        'create_user_id' => 'required',
    );

    public function getFullTitleAttribute(){
      return $this->body.$this->title;
    }

    public function task_comments(){
      return $this->hasMany('App\Models\TaskComment','task_id')->orderBy('created_at','desc');
    }

    public function curriculums(){
      return $this->belongsToMany('App\Models\Curriculum','task_curriculum','task_id','curriculum_id')->withTimestamps();
    }

    public function task_reviews(){
      return $this->hasMany('App\Models\TaskReview', 'task_id');
    }

    public function milestones(){
      return $this->belongsTo('App\Models\Milestone', 'milestone_id');
    }

    public function textbooks(){
      return $this->morphToMany('App\Models\Textbook', 'textbookable')->withTimestamps();
    }

    public function scopeSearchWord($query, $word){
      $search_words = $this->get_search_word_array($word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('body','like', $_like)
            ->orWhere('title','like', $_like);
        }
      });
      return $query;
    }

    public function scopeVisible($query){
      return $query->whereNotIn('status',['cancel']);
    }

    public function change($form, $file=null, $is_file_delete = false, $curriculum_ids = null){
      $s3_url = '';
      $_form = $this->file_upload($file);
      $form['s3_url'] = $_form['s3_url'];
      $form['s3_alias'] = $_form['s3_alias'];
      if($is_file_delete == true){
        $form['s3_url'] = "";
        $form['s3_alias'] = "";
      }
      if($is_file_delete==true){
        //削除指示がある、もしくは、更新する場合、S3から削除
        $this->s3_delete($this->s3_url);
      }
      $this->update($form);
      $this->curriculums()->sync($curriculum_ids);
      return $this;
    }



    public function dispose(){
      if(isset($this->s3_url) && !empty($this->s3_url)){
        //S3アップロードファイルがある場合は削除
        $this->s3_delete($this->s3_url);
      }
      $this->delete();
    }

}
