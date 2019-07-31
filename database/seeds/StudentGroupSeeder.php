<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
use App\Models\UserCalendar;
use App\Models\UserCalendarSetting;

class StudentGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      StudentGroup::truncate();
      StudentGroupMember::truncate();
      $calendars = UserCalendar::whereIn('work',[7,8])->get();
      foreach($calendars as $calendar){
        $this->student_group_add($calendar->details(1), "カレンダ－(".$calendar["datetime"].")");
      }
      $settings = UserCalendarSetting::whereIn('work',[7,8])->get();
      foreach($settings as $setting){
        $this->student_group_add($setting, "通常授業設定(".$setting['week_setting'].' / '.$setting['timezone'].")");
      }
    }
    private function student_group_add($calendar, $remark = ""){
      echo "\nstudent_group_add:";
      $teacher_name = "";
      $student_name = "";
      $other_name = "";
      $teacher_id = 0;
      $student_ids = [];
      $t = Teacher::where('user_id', $calendar->user_id)->first();
      $teacher_id = $t->id;
      foreach($calendar->members as $member){
        $s = Student::where('user_id', $member->user_id)->first();
        if(isset($s)){
          $student_name.=$s->name_last.'・';
          $student_ids[] = $s->id;
        }
      }
      $student_name = trim($student_name, '・');
      $title = "";
      $type = "group";
      if(count($student_ids) < 1) return null;
      if(intval($calendar->work)==8){
        $title = "";
        $type = "family";
      }
      $title = ''.$student_name.'';
      echo $title.'/'.$teacher_id;
      if($this->is_exist_student_group($teacher_id, $student_ids)==true){
        echo ":登録済み";
        return null;
      }
      $group = StudentGroup::add([
        'title' => $title,
        'type' => $type,
        'remark' => $remark,
        'student_id'=> $student_ids,
        'teacher_id'=> $teacher_id,
        'create_user_id'=>1,
      ]);
      return $group;
    }
    private function is_exist_student_group($teacher_id,$student_ids){
      //講師のグループを取得
      $groups = StudentGroup::where('teacher_id', $teacher_id)->get();
      foreach($groups as $group){
        if(count($student_ids) == count($group->members)){
          //同じ生徒のグループの場合すでに存在
          if($group->is_members($student_ids)) return true;
        }
      }
      return false;
    }
}
