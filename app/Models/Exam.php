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
      'type',
      "student_id",
      "grade",
      "semester_no",
      "remark",
    ];
    protected $attributes = [
      'create_user_id' => 1,
    ];

    public function getFullTitleAttribute(){
      return $this->semester_name.":".$this->name;
    }

    public function scopeSearch($query, $request){
      if($request->has('search_grade')){
        $query->grades($request->get('search_grade'));
      }
      if($request->has('order_by')){
        $query->orderBy($request->get('order_by'),'asc');
      }else{
        $query->orderBy('semester_no','asc');
      }
      return $query;
    }

    public function add($form){
      $this->fill($form);
      $this->save();
    }
}
