<?php

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\GeneralAttribute;
use Illuminate\Support\Facades\DB;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('subjects')->truncate();
        $subjects = GeneralAttribute::where('attribute_key','charge_subject')->get();
        $i = 1;
        foreach($subjects as $subject){
          $form = [
            'name' => $subject->attribute_name,
            'create_user_id' => 1,
            'sort_no' => $i,
           ];
           Subject::create($form);
           $i++;
        }
    }
}
