<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class ExamResult extends SchoolGradeReport
{
  use Common;
  protected $table = "exam_results";
    //
    public function exam_resultable(){
      return $this->morphTo();
    }

    public function subject(){
      return $this->belongsTo('App\Models\Subject');
    }

    protected $fillable = [
      "subject_id",
      "average_point",
      "deviation",
      "max_point",
      "taken_date",
      "point",
      "s3_url",
      "s3_alias",
    ];

    protected $attributes =[
      "create_user_id" => 1,
    ];

    public function getPointPerMaxAttribute(){
      return $this->point."/".$this->max_point;
    }

    public function add($form){
      $this->fill($form);
      $exam = Exam::find($form['exam_id']);
      $exam_results = $exam->exam_results()->where('subject_id',$form['subject_id'])->get();

      if($exam_results->count() > 0){
        $exam_results->first()->fill($form)->save();
      }else{
        $exam->exam_results()->save($this);
      }

      return $this;
    }
}
