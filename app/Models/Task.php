<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends MIlestone
{
    //

    public function teachers(){
      return $this->belongsToMany('App\Models\Teacher');
    }

    public function milestones(){
      return $this->belongsToMany('App\Models\Milestone');
    }

    public function textbook(){
      return $this->belogsToMany('App\Models\Textbook');
    }
}
