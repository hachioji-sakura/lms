<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolGrade extends Milestone
{
  protected $table = 'lms.school_grades';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );

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
  public function reports(){
    return $this->hasMany('App\Models\SchoolGradeReport');
    //相手先のMigrateに自分を参照するためのIDが用意されていれば、「hasmany」「hasone」が使用できる。
  }


}
