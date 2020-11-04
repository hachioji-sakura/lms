<?php

use Illuminate\Database\Seeder;
use App\Models\Trial;
use App\Models\Agreement;
use App\Models\Ask;


class AgreementAskUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //カレンダー設定から契約作成
        $this->check_calendar_member_setting();
        //依頼更新
        $this->check_ask();

    }

    public function check_ask(){
      //契約を先に作らないとダメ
      $target_asks = Ask::where('type','agreement')->where('status','new');
      foreach($target_asks as $ask){
        $agreement_id = $ask->student->agreementsByStatus('new')->first()->id;
        $ask->update(['target_model' => 'agreement', 'target_model_id' => $agreement_id]);
      }
    }

    public function check_calendar_member_setting(){
      $target_trials = Trial::whereIn('status',['entry_contact','entry_hope'])->get();
      $target_members = $target_trials->map(function($item){
        return $item->student->user->calendar_member_settings()->where('status','fix')->first();
      });
      $new_agreement = [];
      foreach($target_members as $member){
        if(!empty($member)){
          $new_agreement[] = Agreement::add_from_member_setting($member);
        }
      }
    }
}
