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
        $default_grade = $student->default_grade();
        UserTag::setTag($student->user_id,'grade',$default_grade,1);
      }
    }
}
