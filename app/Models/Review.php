<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Review extends Milestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.reviews';
    protected $guarded = array('id');

    public static $rules = array(
        'task_id' => 'required',
        'body' => 'required',
        'create_user_id' => 'required',
    );

    public function tasks(){
      return $this->belongsTo('App\Models\Task','task_id');
    }

}
