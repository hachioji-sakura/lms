<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
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

    public function scopeActiveTasks($query){
      return $query->whereIn('status',["new","progress"]);;
    }


    public function task_comments(){
      return $this->hasMany('App\Models\TaskComment','task_id')->orderBy('created_at','desc');
    }

    public function curriculums(){
      return $this->belongsToMany('App\Models\Curriculum','task_curriculum','task_id','curriculum_id')->withTimestamps();
    }

    public function reviews(){
      return $this->hasMany('App\Models\Review', 'task_id');
    }

    public function milestones(){
      return $this->belongsTo('App\Models\Milestone', 'milestone_id');
    }

    public function textbooks(){
      return $this->belogsToMany('App\Models\Textbook');
    }

    public function scopeSearch($query,$request){
      $search_status = $request->query('search_status','active');
      if($search_status == 'active'){
        $query = $query->activeTasks();
      }else{
        $query = $query->findStatuses($request->get('search_status'));
      }
      if($request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      if($request->has('search_type')){
        $query = $query->findTypes($request->get('search_type'));
      }
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
