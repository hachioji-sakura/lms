<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReview extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.task_reviews';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
        'type' => 'required'
    );

    public function tasks(){
      return $query->belongsTo('App\Models\Task','task_id');
    }

}
