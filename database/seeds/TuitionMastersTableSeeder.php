<?php

use Illuminate\Database\Seeder;
use App\Models\GeneralAttribute;
use App\Models\TuitionMaster;

class TuitionMastersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        /*
          lesson_id
          1　塾
          2 英会話
          3 ピアノ
          4 習い事

          grade
          1 幼児
          2 小学校1年
          5　小学校4年　→中学受験？
          8　中学校1年
          11　高校1年

          course_id
          1 setting
          2 group

          subject
          5 soloban
          43 chinese
          49 dance
        */
        //2020/4時点で有効なマスターのみ抽出して救う
        $where_raw = <<<EOT
              lesson_fee != 0
              and (
                  date (concat(start_month, "/01")) >= date ("2020/02/01")
                  or (
                      start_month = ""
                      and (
                          end_month = ""
                          or date (concat(end_month, "/01")) > date ("2020/04/01")
                      )
                      or (lesson_id = 2 and start_month = "2019/08")
                  )
              );
EOT;
        $old_fees = DB::table('hachiojisakura_management.tbl_lesson_fee')->whereRaw($where_raw)->get();
        DB::transaction(function() use ($old_fees){
          DB::table('common.tuition_masters')->truncate();
          $this->create_fee_master($old_fees);
        });
    }

    public function create_fee_master($old_fees){
      $course_types = [
        '1' => 'single',
        '2' => 'group',
      ];
      $subjects = [
        '5' => 'soloban',
        '43' => 'chinese',
        '49' => 'dance',
      ];

      foreach($old_fees as $old_fee){
        $all_grades = GeneralAttribute::findKey('grade');
        $grades = '';
        switch ($old_fee->lesson_grade){
          case 1:
            $grades = $all_grades->where('attribute_value','like',"k%")->get();
            break;
          case 2:
          case 5:
            $grades = $all_grades->where('attribute_value','like',"e%")->get();
            break;
          case 8:
            $grades = $all_grades->where('attribute_value','like',"j%")->get();
            break;
          case 11:
            $grades = $all_grades->where('attribute_value','like',"h%")->get();
            break;
        }
        if(empty($grades)){
          continue;
        }
        if(empty($old_fee->subject_id)){
          $subject = null;
        }else{
          $subject = $subjects[$old_fee->subject_id];
        }
        $form = [];
        foreach($grades as $grade){
          $form[] = [
            'title' => config('attribute.lesson')[$old_fee->lesson_id].'/'.$course_types[$old_fee->course_id].'/'.$grade->attribute_value.'/'.$old_fee->lesson_length.'m/週'.$old_fee->lesson_count.'回',
            'grade' => $grade->attribute_value,
            'fee' => $old_fee->lesson_fee,
            'lesson' => $old_fee->lesson_id,
            'course_type' => $course_types[$old_fee->course_id],
            'course_minutes' => $old_fee->lesson_length,
            'lesson_week_count' => $old_fee->lesson_count,
            'subject' => $subject,
            'is_exam' => $old_fee->jyukensei_flag,
            'start_date' => $this->get_date($old_fee->start_month,"start"),
            'end_date' => $this->get_date($old_fee->end_month,"end"),
            'create_user_id' => 1,
            'created_at' => date('Y/m/d H:i:s'),
            'updated_at' => date('Y/m/d H:i:s'),
          ];
        }
        $fee_master = new TuitionMaster;
        $res = $fee_master->insert($form);
      }
    }

    public function get_date($date,$type){
      if( empty($date) ){
        //開始日空っぽなら一律2000年
        if($type == "start"){
          return date('Y/m/d',strtotime("2000/01/01"));
        }elseif($type == "end"){
          return null;
        }
      }else{
        //YYYY/mmなので、日を追加返す
        return date('Y/m/d',strtotime($date.'/01'));
      }
    }
}
