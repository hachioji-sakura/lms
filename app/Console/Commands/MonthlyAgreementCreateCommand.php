<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agreement;
use DB;
use App\User;

class MonthlyAgreementCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agreements:create {year?} {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Monthly Agreements';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      DB::transaction(function(){
        if(empty($this->argument('year')) || empty($this->argument('month')) ){
          $date = date('Y-m-d');
        }else{
          $date = date('Y-m-d',strtotime($this->argument('year')."-".$this->argument('month')));
        }
        $student_users = User::has('student')->get();
        $target_users = $student_users->filter(function($item) use($date){
          //契約の対象になるカレンダー設定があるユーザ
          return $item->monthly_enable_calendar_settings($date)->count() > 0;
        });
        //体験生徒と職員を除く
        $target_users = $target_users->reject(function($item){
            return $item->id == 888 || $item->id == 890;
        });
        foreach($target_users as $user){
            $agreement = new Agreement;
            $member_setting = $user->calendar_member_settings()->first();
            $new_agreement = $agreement->add_from_member_setting($member_setting->id,$date);
            $new_agreement->remark = "Agreement of ".$date." from batch.";
            $new_agreement->save();
        }

        echo "Agreements were created successfull.";
      });
    }
}
