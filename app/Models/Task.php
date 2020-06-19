<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaskReview;
use DB;


class Task extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.tasks';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'status' => 'required',
        'target_user_id' => 'required',
        'create_user_id' => 'required',
    );

    public function scopeActiveTasks($query, $statuses){
      return $query->whereIn('status',$statuses);
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

    public function scopeSearch($query,$request){
      $search_status = $request->query('search_status','active');
      $search_type = $request->get('search_type');
      if($search_status == 'active'){
        if($search_type == "homework"){
          $statuses = ['new','progress'];
        }elseif($search_type == "class_record"){
          $statuses = ["done"];
        }else{
          $statuses = ['new','progress'];
        }
        $query = $query->activeTasks($statuses);
      }else{
        $query = $query->findStatuses($search_status);
      }
      if($request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      if($request->has('search_type')){
        $query = $query->findTypes($request->get('search_type'));
      }
      if(!empty($request->get('eval_min_value')) && !empty($request->get('eval_max_value'))){
        $query = $query->reviewEvaluationRange($request->get('eval_min_value'), $request->get('eval_max_value'));
      }
      if(!empty($request->get('search_from_date')) || !empty($request->get('search_to_date'))){
        $query = $query->rangeDate($request->get('search_from_date'),$request->get('search_to_date'));
      }

      return $query;
    }

    public function scopeReviewEvaluationRange($query, $min_value, $max_value){
      return $query->whereHas('task_reviews', function($query) use ($min_value, $max_value) {
          $query->whereBetween('evaluation',[$min_value,$max_value]);
      });
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
