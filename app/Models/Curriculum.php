<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'lms.curriculums';
    protected $guarded = array('id');

    public function curriculum_tags(){
      return $this->hasMany('App\Models\CurriculumTag','curriculum_id');
    }
    
    public function subjects(){
      return $this->belogsToMany('App\Models\Subject');
    }
}
