<?php

use Illuminate\Database\Seeder;
use App\Models\Student;

class EntryDateSeeder extends Seeder
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
          $this->update_entry_date($old_data);
        });
    }

    public function update_entry_date($old_data){
      $students = Student::findStatuses(['regular'])->get();
      foreach($students as $student){
        $student_no = $student->user->get_tag('student_no')->tag_value;
        $target_old_data = $old_data->filter(function($item) use($student_no){
          return $item->no == $student_no;
        });
        if($target_old_data->count() > 0){
          $student->entry_date = $target_old_data->first()->entry_date;
          $student->save();
        }
      }
    }

    public function get_old_data(){
      $sql = <<<EOT
      select
          tmem.no,
          tmem.name,
          from_unixtime(min(tsd.start_timestamp)) as entry_date
      from
          hachiojisakura_management.tbl_statement_detail tsd
          inner join hachiojisakura_management.tbl_statement tst
              on tsd.statement_no = tst.statement_no
          inner join hachiojisakura_management.tbl_member tmem
              on tst.member_no = tmem.no
      group by
          tmem.name, tmem.no
      order by entry_date desc
EOT;
      $data = DB::select($sql);
      return collect($data);
    }
}
