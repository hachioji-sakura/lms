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
      return $this->belongsToMany('App\Models\Subject')->withTimestamps();
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
      $this->delete();
      return $this;
    }


}
