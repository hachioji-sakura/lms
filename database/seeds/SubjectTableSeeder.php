<?php

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\GeneralAttribute;

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
        $subjects = GeneralAttribute::where('attribute_key','charge_subject')->get();
        foreach($subjects as $subject){
          $form = [
            'name' => $subject->attribute_name,
            'create_user_id' => 1,
           ];
           Subject::create($form);
        }
    }
}
