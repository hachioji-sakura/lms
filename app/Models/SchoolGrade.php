<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class SchoolGrade extends Milestone
{

  use Common;

  protected $table = 'lms.school_grades';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );

  protected $fillable = [
    "title",
    "student_id",
    "grade",
    "semester_no",
    "remark",
    "s3_alias",
    "s3_url",
  ];

  protected $appends = ['student_name','grade_name','semester_name'];

  //　学年名設定処理
 public function getGradeNameAttribute(){
   //return 'hoge';
   $grade_name = "";
   if(app()->getLocale()=='en') return $this->grade;

   if(isset(config('grade')[$this->grade])){
     $grade_name = config('grade')[$this->grade];
   }
   //dd($grade_name);
   return $grade_name;
 }

 //生徒名を呼び出す処理 (Tableリレーション)
   public function getStudentNameAttribute(){
     //dd($this->student->name());
     return $this->student->name();
   }

 //　学期名設定処理
public function getSemesterNameAttribute(){
  //return 'hoge';
  $semester_name = "";
  if(app()->getLocale()=='en') return $this->semester_no;

  if(isset(config('attribute.semester_no')[$this->semester_no])){
    $semester_name = config('attribute.semester_no')[$this->semester_no];
  }
  //dd($grade_name);
  return $semester_name;
}

public function getSchoolGradeReportPointsAttribute(){
  $items =  $this->school_grade_reports->pluck('report_point','subject_name')->toArray();
  $ret = [];
  foreach($items as $key => $value){
    $ret[] = $key.":".$value;
  }
  return $ret;
}

//belongsTo　=　あのテーブルは”俺”のデータを持っている(俺はあのテーブルに属している) という意味
//hasone = ”俺”はあのテーブルのデータを1つ持つ という意味
//hasmany = ”俺”はあのテーブルのデータをたくさん持つ という意味
//Model間のデータ参照が「$this->…」形式で取得できるようになる

//ここでは、Student＞SchoolGrade＞SchoolGradeReport　の順で関連してる
//studentが親なので「belongsTo」
  public function student(){
    return $this->belongsTo('App\Models\Student');
    //自分のMigrateに相手先を参照するためのIDが用意されていれば、「belongsto」が使用できる。
  }

//SchoolGradeReportは子なので「hasmeny」or「hasone」
  public function school_grade_reports(){
    return $this->hasMany('App\Models\SchoolGradeReport');
    //相手先のMigrateに自分を参照するためのIDが用意されていれば、「hasmany」「hasone」が使用できる。
  }

  public function scopeGrades($query,$grades){
    return $query->whereIn('grade',$grades);
  }

  public function scopeSemesterNos($query,$semester_nos){
    return $query->whereIn('semester_no',$semester_nos);
  }

  public function scopeStudentIds($query,$student_ids){
    return $query->whereIn('student_id',$student_ids);
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

  public function scopeFindExistings($query, $grade,$semester_no, $student_id){
    return $query->grades([$grade])->semesterNos([$semester_no])->studentIds([$student_id]);
  }

  public function add($form){
    if(!isset($form['student_id'])){
      $form['student_id'] = $this->student_id;
    }
    $existings = self::findExistings($form['grade'],$form['semester_no'],$form['student_id']);
    if($existings->count() > 0 ){
      $item = $existings->first();
    }else{
      $item = $this;
      $title = config("grade")[$form['grade']]."/".config("attribute.semester_no")[$form["semester_no"]];
      $item->fill($form);
      $item->title = $title;
      $item->save();
    }

    $subjects = $form["subject"];
    $report_points = $form["report_point"];

    $is_null = false;
    if(count(array_unique($subjects)) == 1 && array_unique($subjects)[0] === null){
      $is_null = true;
    }

    if($is_null == false){
      $item->school_grade_reports()->delete();
      foreach($subjects as $i => $subject){
        $item->school_grade_reports()->updateOrCreate(["subject_id" => $subject],["report_point" => $report_points[$i]]);
      }
    }

    return $item;
  }

  public function dispose(){
    $this->school_grade_reports()->delete();
    $this->delete();
  }

}
