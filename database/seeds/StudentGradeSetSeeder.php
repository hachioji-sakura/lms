<?php

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\UserTag;
class StudentGradeSetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $students = Student::findStatuses(['regular', 'recess'])->get();
      foreach($students as $student){
        if(empty($student->age())){
          //年齢が取得できない場合、学年の定義はできないので、いったん削除
          UserTag::clearTags($student->user_id,'grade');
        }
        //toddler → k1 or k2 or k3に定義変更した
        if(!empty($student->grade()) && $student->grade()!='toddler') continue;
        $default_grade = $student->default_grade();
        UserTag::setTag($student->user_id,'grade',$default_grade,1);
      }
    }
}
