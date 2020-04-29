<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends MIlestone
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.tasks';
    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'remarks' => 'required',
        'status' => 'required',
        'target_user_id' => 'required',
        'create_user_id' => 'required',
        'start_schedule' => 'required',
        'end_schedule' => 'required',
    );

    public function teachers(){
      return $this->belongsToMany('App\Models\Teacher');
    }


    public function milestones(){
      return $this->belongsToMany('App\Models\Milestone');
    }

    public function textbook(){
      return $this->belogsToMany('App\Models\Textbook');
    }

    public function dispose(){
      /*
      if(isset($this->s3_url) && !empty($this->s3_url)){
        //S3アップロードファイルがある場合は削除
        $this->s3_delete($this->s3_url);
      }
      */
      $this->delete();
    }

}
