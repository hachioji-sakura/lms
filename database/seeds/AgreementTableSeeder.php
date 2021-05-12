<?php

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Ask;
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
        //ユーザーが確認中のもの以外を削除して
        $ret = $this->target_delete();
        //カレンダー設定から契約を作って料金を旧から入れる
        $this->_add_agreement($ret['except_student_ids']);
      });
    }


    public function target_delete(){
      //commit,dummy,cancelのものはすべて削除
      $commit_ag = Agreement::whereIn('status',['commit','dummy','cancel'])->get();
      $commit_ag->map(function($item){
        return $item->dispose();
      });
      //statusがnewのやつは削除しない
      $except_student_ids = Agreement::where('status','new')->get()->pluck('student_id');
      return ['except_student_ids' => $except_student_ids ];
    }

    public function _add_agreement($except_student_ids, $is_test = false){
      $target_users = User::has('enable_calendar_member_settings')->has('student')->get();

      if($is_test){
        //メンテナンス用
        //3月分
        $old_tbl_fee = DB::table('hachiojisakura_management_202103.tbl_fee')->get();
        $old_tbl_member = DB::table('hachiojisakura_management_202103.tbl_member')->get();
        $old_entrance_fees = DB::table('hachiojisakura_management_202103.tbl_entrance_fee')->get();
      }else{
        $old_tbl_fee = DB::table('hachiojisakura_management.tbl_fee')->get();
        $old_tbl_member = DB::table('hachiojisakura_management.tbl_member')->get();
        $old_entrance_fees = DB::table('hachiojisakura_management.tbl_entrance_fee')->get();
      }

      //体験生徒と職員を除く
      $target_users = $target_users->reject(function($item){
        return $item->id == 888 || $item->id == 890;
      });
      foreach($target_users as $user){
        $agreement = new Agreement;
        $member_setting = $user->calendar_member_settings()->first();

        //のこした契約の人は更新しない
        if($except_student_ids->contains($user->student->id)){
          continue;
        }

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
        $new_agreement->status = "commit";//承認済みで登録
        $new_agreement->start_date = "2000-01-01";
        if($user->details()->status == "unsubscribe"){
          //退会してたら終了日セット
          $new_agreement->end_date = "2000-01-01";
        }
        $new_agreement->remark = "enforce registered";
        $new_agreement->save();

        //明細のターン
        foreach($new_agreement->agreement_statements as $statement){
          $long_teacher_id = Teacher::find($statement->teacher_id)->user->get_tag_value('teacher_no');
          $teacher_id = preg_replace('/^10+([0-9]*)$/','$1',$long_teacher_id);
          $course_id = 0;
          $_course_id = ["single" => 1, "group" => 2];
          if(isset($_course_id[$statement->course_type])){
            $course_id = $_course_id[$statement->course_type];
          }
          //講師と部門で料金をひっかける
          $old_fee = $old_tbl_fee->where('member_no',$student_no)
                      ->where('lesson_id',$statement->lesson_id)
                      ->where('teacher_id',$teacher_id)
                      ->where('course_id', $course_id)->sortByDesc('insert_timestamp');
          //なかったら最新
          /*
          if($old_fee->count() > 1){
            echo "no:".$student_no."/".$new_agreement->title."/old:".$old_fee->first()->fee."/new:".$statement->tuition.PHP_EOL;
            $old_price = $old_fee->first()->fee;
            $tmp_o_f = $old_fee->where('fee','!=',$old_price);
            if($tmp_o_f->count() > 0){
              dd($old_fee);
            }
          }
          */
          if($old_fee->count() > 0){
            $fee = $old_fee->first()->fee;
            $statement->tuition = $fee;
          }
          $statement->save();
        }
      }
    }
}
