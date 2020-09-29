<?php

use Illuminate\Database\Seeder;
use App\Models\Student;
class StudentGradeUpdateSeeder extends Seeder
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
          $student->gradeUp();
        }
    }
}
