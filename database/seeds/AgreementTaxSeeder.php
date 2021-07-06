<?php

use Illuminate\Database\Seeder;
use App\Models\Student;

class AgreementTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //もともと税込みの生徒の契約を税込みにする
        DB::transaction(function () {
            $old_data  = $this->get_old_data();
            $this->update_agreement($old_data);
        });
    }

    public function get_old_data(){
        //税込みの生徒一覧を取得
        $sql = <<<EOT
            select no, name from hachiojisakura_management.tbl_member where del_flag = 0 and tax_flag = 0
EOT;
        $old_data = collect(DB::select($sql));
        return $old_data;
    }

    public function update_agreement($datam){
        foreach($datam as $data){
            //税込みの対象生徒を取得
            $student = Student::hasTag('student_no',abs($data->no))->first();
            if($student->agreements->count() > 0){
                //契約を持っていたら全て税込みに変更
                $student->agreements()->update([
                    'consumption_tax_rate' => config('attribute.taxes')["consumption_tax_rate"],
                    'consumption_tax_type' => "include",
                ]);
            }
        }
    }
}
