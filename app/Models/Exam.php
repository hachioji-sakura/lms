<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends SchoolGrade
{
    //
    protected $table = 'lms.exams';
    public function exam_results(){
      return $this->morphMany('App\Models\ExamResult','exam_resultable');
    }

    protected $fillable = [
      "name",
      "student_id",
      "remark",
    ];
    protected $attributes = [
      'create_user_id' => 1,
    ];

    public function add($form){
      $this->fill($form);
      $this->save();

      $this->exam_results()->createMany([
        ["subject_id" => 3,"taken_date" => date('Y-m-d',strtotime('now')),"point" => 90],
        ["subject_id" => 4,"taken_date" => date('Y-m-d',strtotime('now')),"point" => 80],
      ]);
    }
}
