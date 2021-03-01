<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    //
    public function exam_resultable(){
      return $this->morphTo();
    }

    public function subject(){
      return $this->belongsTo('App\Models\Subject');
    }

    protected $fillable = [
      "subject_id",
      "taken_date",
      "point",
    ];

    protected $attributes =[
      "create_user_id" => 1,
    ];
}
