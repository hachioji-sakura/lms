<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaskReview;
use DB;
use App\Models\Traits\Scopes;
use App\Models\Traits\Common;

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
      return $this->belogsToMany('App\Models\Textbook');
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
