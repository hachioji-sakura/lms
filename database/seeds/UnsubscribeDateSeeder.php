<?php

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendar;

class UnsubscribeDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //
         DB::transaction(function(){
            $old_data = $this->get_old_data();
            $this->update_unsubscribe_date($old_data);
         });
    }

   public function update_unsubscribe_date($old_data){
      $students = Student::where('status', 'unsubscribe')->get();
      foreach($students as $student){
         if(!empty($student->unsubscribe_date)) continue;
         $student_no = $student->user->get_tag('student_no')->tag_value;
         echo "student_id=".$student->id."/entry_date=".$student->entry_date."/student_no=".$student_no."/unsubscribe_date=";
         $target_old_data = $old_data->filter(function($item) use($student_no){
            return $item->no == $student_no;
         });
         if($target_old_data->count() > 0){
            echo $target_old_data->first()->unsubscribe_date;
            $student->unsubscribe_date = $target_old_data->first()->unsubscribe_date;
            $student->save();
         }
         echo "\n";
      }
      foreach($students as $student){
         if(!empty($student->unsubscribe_date)) continue;
         $student->unsubscribe_date = '2000-01-01';
         $student->save();
      }
   }

   public function get_old_data(){
      $sql = <<<EOT
      select
      tmem.no
      , tmem.name
      , from_unixtime(max(tsd.end_timestamp)) as unsubscribe_date 
   from
      hachiojisakura_management.tbl_statement_detail tsd 
      inner join hachiojisakura_management.tbl_statement tst 
         on tsd.statement_no = tst.statement_no 
      inner join hachiojisakura_management.tbl_member tmem 
         on tst.member_no = tmem.no 
   group by
      tmem.name
      , tmem.no 
   order by
      unsubscribe_date desc      
EOT;
      $data = DB::select($sql);
      return collect($data);
   }
}
