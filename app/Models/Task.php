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
      return $query->whereIn('status',["new","progress","done"]);;
    }

    public function task_comments(){
      return $this->hasMany('App\Models\TaskComment','task_id')->orderBy('created_at','desc');
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

    public function scopeSearchQuery($query,$request){
      if($request->has('search_status') ){
        if($request->get('search_status') == 'active'){
          $query = $query->activeTasks();
        }else{
          $query = $query->findStatuses($request->get('search_status'));
        }
      }
      if($request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      return $query;
    }

    public function scopeSearchWord($query, $word){
      $search_words = explode(' ', $word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('remarks','like', $_like)
            ->orWhere('title','like', $_like);
        }
      });
      return $query;
    }

    public function status_count($user_id = null){
      if(empty($user_id)){
        $query = $this->query();
      }else{
        $query = $this->query()->findTargetUser($user_id);
      }
      $status_count['all'] = $query->count();
      $counts = $query->select(DB::raw('count(*) as count,status'))->groupBy('status')->get();
      foreach($counts as $count){
        $status_count[$count['status']] = $count['count'];
      }
      return $status_count;
    }

    public function dispose(){
      if(isset($this->s3_url) && !empty($this->s3_url)){
        //S3アップロードファイルがある場合は削除
        $this->s3_delete($this->s3_url);
      }
      $this->delete();
    }

}
