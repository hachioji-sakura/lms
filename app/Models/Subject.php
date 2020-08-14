<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.subjects';
    protected $guarded = array('id');

    public function curriculums(){
      return $this->belongsToMany('App\Models\Curriculum','curriculum_subject','curriculum_id','subject_id')->withTimestamps();
    }

    public function details(){
      $item = $this;
      $item["created_date"] = $this->created_at_label();
      $item["updated_date"] = $this->updated_at_label();
      $item["create_user_name"] = $this->create_user->details()->name();
      return $item;
    }

    public function update_curriculum($form, $curriculum_ids = null ){
      $this->update($form);
      $this->curriculums()->sync($curriculum_ids);
      return $this;
    }

    public function scopeSearch($query,$request){
      if($request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      return $query;
    }

    public function scopeSearchWord($query, $word){
      $search_words = explode(' ', urlencode($word));
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('remarks','like', $_like)
            ->orWhere('name','like', $_like);
        }
      });
      return $query;
    }

    public function dispose(){
      $this->curriculums()->detach();
      $this->delete();
      return $this;
    }



}
