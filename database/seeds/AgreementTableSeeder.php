<?php

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Agreement;
use App\Models\AgreementStatement;
use App\Models\UserCalendarMemberSetting;
use App\User;

class AgreementTableSeeder extends Seeder
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
        $this->add_agreement();
//        $this->update_agreement_statement();
      });
    }

    public function add_agreement(){
      $target_users = User::has('enable_calendar_member_settings')->has('student')->get();
      $old_tbl_fee = DB::table('hachiojisakura_management.tbl_fee')->get();
      $old_tbl_member = DB::table('hachiojisakura_management.tbl_member')->get();
      $old_entrance_fees = DB::table('hachiojisakura_management.tbl_entrance_fee')->get();

      //体験生徒と職員を除く
      $target_users = $target_users->reject(function($item){
        return $item->id == 888 || $item->id == 890;
      });
      foreach($target_users as $user){
        $agreement = new Agreement;
        $member_setting = $user->calendar_member_settings()->first();
        /*
        $student = Student::where("user_id",$user->id)->first();
        $ret = $this->get_agreement_data_source($student->name);
        $form = [
          'title' => $student->name.":".date('Y/m/d'),
          'type' => 'normal',
          'student_id' => $student->id,
          'status' => 'new',
          'entry_fee' => $ret["entry_fee"], //スプレッドシートの値を持ってくる
          'monthly_fee' => $ret["monthly_fee"], //スプレッドシートの値を持ってくる
          'student_parent_id' => $student->relations->first()->student_parent_id,
        ];
        $agreement = new Agreement($form);
        $agreement->save();

        $forms =  $this->get_statement_update_array()->where('name',$student->name);
        if($forms->count()>0){
          $forms->map(function($item){
            return $item->pull("name");
          });
          $forms->map(function($item) use ($student){
            $count = $student->user->get_enable_calendar_setting_count($item["lesson_id"]);
            return $item->put("lesson_week_count",$count);
          });
          $forms = $forms->toArray();
          $agreement->agreement_statements()->createMany($forms);
        }
        */

        //既存の物を現状のロジックで契約追加
        $new_agreement = $agreement->add_from_member_setting($member_setting->id);

        //旧情報で上書き
        //月会費
        $student_no = str_pad($user->get_tag_value('student_no'),6,0,STR_PAD_LEFT);
        $old_member = $old_tbl_member->where('no',$student_no);
        if($old_member->count() > 0){
          $new_agreement->monthly_fee = $old_member->first()->membership_fee;
        }else{
          //なかったら0円
          $new_agreement->monthly_fee = 0;
        }
        //入会金
        $old_entrance_fee = $old_entrance_fees->where('member_no',$student_no);
        if($old_entrance_fee->count() > 0){
          $entry_fee = $old_entrance_fee->first()->price;
        }else{
          //なかったら0
          $entry_fee = 0;
        }
        $new_agreement->entry_fee = $entry_fee;
        $new_agreement->status = "dummy";//中間ステータスで登録
        $new_agreement->start_date = date('Y-m-d H:i:s');
        $new_agreement->remark = "enforce registered";
        $new_agreement->save();

        //明細のターン
        foreach($new_agreement->agreement_statements as $statement){
          $long_teacher_id = Teacher::find($statement->teacher_id)->user->get_tag_value('teacher_no');
          $teacher_id = preg_replace('/^10+([0-9]*)$/','$1',$long_teacher_id);
          //講師と部門で料金をひっかける
          $old_fee = $old_tbl_fee->where('member_no',$student_no)
                      ->where('lesson_id',$statement->lesson_id)
                      ->where('teacher_id',$teacher_id)->sortByDesc('insert_timestamp');
          //なかったら0
          if($old_fee->count() > 0){
            $fee = $old_fee->first()->fee;
          }else{
            $fee = 0;
          }
          $statement->tuition = $fee;
          $statement->save();
        }
      }
    }

    public function update_agreement_statement(){
      /*
      生徒名・部門・コースタイプ・時間をキーにしてひっかける
      料金は1時間単価で入力
      生徒の学年もついでに更新する？
      */
      $update_array = $this->get_statement_update_array();
      foreach($update_array as $fields){
        $student = Student::all()->where("name",$fields["name"])->first();
        $statement = $student->agreements->map(function($item){
          return $item->agreement_statements;
        })->first();
        foreach($fields as $field => $value){
          if($field != "name" && $field != "tuition"){
            $statement = $statement->where($field,$value);
          }
        }
        $st = $statement->first();

        $st->tuition = $fields["tuition"];
        $st->save();
      }
    }

    public function get_agreement_data_source($student_name){
      $ret = $this->get_agreement_update_array()->where("name",$student_name)->first();
      if(empty($ret)){
        return [ "entry_fee" => 0, "monthly_fee" => 0];
      }
      return $ret;
    }



    public function get_agreement_update_array(){
      return collect([
        [
          "name" => "伊勢崎 あい",
          "entry_fee" => 100000000,
          "monthly_fee" => 300000000,
        ],
        [
          "name" => "北川 賢治",
          "entry_fee" => 100000000,
          "monthly_fee" => 300000000,
        ],
      ]);
    }

    public function get_statement_update_array(){
      return collect([
          collect([
            "name" => "津田 和佳子",
            "teacher_id" => "46",
            "lesson_id" => 1,
            "course_type" => "single",
            "course_minutes" => 60,
            "grade" => "e5",
            "tuition" => 200000,
            "is_exam" => 0,
          ]),
          collect([
            "name" => "津田 和佳子",
            "teacher_id" => "46",
            "lesson_id" => 1,
            "course_type" => "single",
            "course_minutes" => 90,
            "grade" => "e5",
            "tuition" => 1500,
            "is_exam" => 0,
          ]),
      ]);
    }
}
