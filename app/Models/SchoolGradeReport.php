<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolGradeReport extends Model
{
    //
    protected $fillable = [
      "school_grade_id",
      "subject_id",
      "report_point",
      "remark",
    ];

    public function subject(){
      return $this->belongsTo('App\Models\Subject','subject_id');
    }

    public function school_grade(){
      return $this->belongsTo('App\Models\SchoolGrade');
    }

    public function getStudentNameAttribute(){
      return $this->school_grade->student_name;
    }

    public function getSemesterNameAttribute(){
      return $this->school_grade->semester_name;
    }

    public function getSubjectNameAttribute(){
      return $this->subject->name;
    }

    public function dispose(){
      $this->delete();
    }
}
