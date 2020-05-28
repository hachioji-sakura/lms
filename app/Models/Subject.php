<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.subjects';
    protected $guarded = array('id');

    public function curriculums(){
      return $this->belongsToMany('App\Models\Curriculum');
    }

}
