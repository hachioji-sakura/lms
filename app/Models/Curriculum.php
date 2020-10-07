<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Curriculum extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.curriculums';
    protected $guarded = array('id');

    public function subjects(){
      return $this->belongsToMany('App\Models\Subject','curriculum_subject','curriculum_id','subject_id')->withTimestamps();
    }

    public function tasks(){
      return $this->belongsToMany('App\Models\Task','task_curriculum','curriculum_id','task_id')->withTimestamps();
    }


    public function details(){
      $item = $this;
      $item["created_date"] = $this->created_at_label();
      $item["updated_date"] = $this->updated_at_label();
      $item["create_user_name"] = $this->create_user->details()->name();
      return $item;
    }

    public function update_curriculum($form, $subject_ids = null ){
      $this->update($form);
      $this->subjects()->sync($subject_ids);
      return $this;
    }

    public function dispose(){
      $this->subjects()->detach();
      $this->tasks()->detach();
      $this->delete();
      return $this;
    }

    public function scopeSearchBySubjectId($query,$subject_id){
      return $query->whereHas('subjects', function($query) use ($subject_id) {
          $query->where('subjects.id', $subject_id);
      });
    }

    public function scopeSearch($query, $request){
      if( $request->has('search_subject_id') && is_numeric( $request->get('search_subject_id'))){
        $search_subject_id = $request->get('search_subject_id');
        $query = $query->searchBySubjectId($search_subject_id);
      }

      if($request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      return $query;
    }

    public function scopeSearchWord($query, $word){
      $search_words = $this->get_search_word_array($word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('remarks','like', $_like)
            ->orWhere('name','like', $_like);
        }
      });
      return $query;
    }
}
