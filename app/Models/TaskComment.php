<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Comment
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.task_comments';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
        'type' => 'requered',
        'target_user_id' => 'required',
        'create_user_id' => 'required',
    );

    public function tasks(){
      return $this->belongsTo('App\Models\Task','task_id');
    }

    public function details(){
     $item = $this;
     $item["type_name"] = $this->type_name();
     $item["created_date"] = $this->created_at_label();
     $item["updated_date"] = $this->updated_at_label();
     $item["create_user_name"] = $this->create_user->details()->name();
     return $item;
   }
}
