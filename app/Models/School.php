<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.schools';
    protected $guarded = array('id');

    public function students(){
      return $this->hasMany('App\Models\Students','school_id','id');
    }

    public function scopeSearch($query, $request){
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
            ->orWhere('name','like', $_like);
        }
      });
      return $query;
    }

    public function details(){
      $item = $this;
      $item["created_date"] = $this->created_at_label();
      $item["updated_date"] = $this->updated_at_label();
      $item["create_user_name"] = $this->create_user->details()->name();
      return $item;
    }
}
