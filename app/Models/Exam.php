<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class Exam extends SchoolGrade
{
    use Common;
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
      "s3_url",
      "s3_alias",
    ];
    protected $attributes = [
      'create_user_id' => 1,
    ];

    public function student(){
      return $this->belongsTo('App\Models\Student','student_id');
    }

    public function getFullTitleAttribute(){
      return $this->semester_name.":".$this->name;
    }

    public function getSumPointAttribute(){
      return $this->exam_results->sum('point');
    }

    public function getSumMaxPointAttribute(){
      return $this->exam_results->sum('max_point');
    }

    public function getSumPointPerMaxAttribute(){
      return $this->sum_point."/".$this->sum_max_point;
    }

    public function getResultCountAttribute(){
      return $this->exam_results->count();
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
      $this->name = config("attribute.exam_type")[$form['type']];
      $this->save();
      return $this;
    }
}
