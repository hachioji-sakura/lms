<?php

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Curriculum;


class AddEnglishConversationSubjectsCurriculumsSeeder extends seeder
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
        $subject_name = '子供英会話';
        $config_name = 'english_conversation_curriclums';
        $curriculum_names = config('data.'.$config_name);

        $old_subject = Subject::where('name',$subject_name);
        if($old_subject->count() > 0 ){
          echo 'Already registered.'.PHP_EOL;
          exit;
        }
        $subject = new Subject;
        $subject->add_with_curriculums_by_name($subject_name,$curriculum_names);
      });
    }
}
