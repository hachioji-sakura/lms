<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgreementStatement extends Model
{
    //
    protected $connection = 'mysql_common';
    protected $table = 'common.agreement_statements';
    protected $guarded = array('id');
    protected $fillable = [
      'title',
      'student_id',
      'teacher_id',
      'agreement_id',
      'tuition',
      'title',
      'lesson',
      'course_type',
      'course_minutes',
      'subject',
      'grade',
      'lesson_week_count',
      'is_exam',
      'remark',
    ];

    public function agreement(){
      return $this->belongsTo('App\Models\Agreement','agreement');
    }

    static protected function add($form){

    }

    public function is_already_registered($form){
      $already_data = AgreementStatement::where('student_id' , $form['student_id'])
      ->where('teacher_id' , $form['teacher_id'])
      ->where('lesson' , $form['lesson'])
      ->where('course_type' , $form['course_type'])
      ->where('course_minutes' , $form['course_minutes'])
      ->where('grade' , $form['grade'])
      ->where('lesson_week_count' , $form['lesson_week_count'])
      ->where('subject' , $form['subject'])
      ->where('agreement_id',$form['agreement_id'])->get();
      if(isset($already_data) && count($already_data)>0){
        \Log::warning("tuition : already");
        return null;
      }
    }

}
