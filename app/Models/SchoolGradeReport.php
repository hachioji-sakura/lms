<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolGradeReport extends Milestone
{
    protected $table = "school_grade_reports";
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

    public function getReportPointNameAttribute(){
      return config('attribute.school_grade_type_points')[$this->school_grade->type][$this->report_point];
    }

    public function dispose(){
      $this->delete();
    }
}
