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
        $target_users = User::has('enable_calendar_member_settings')->has('student')->get();
        //体験生徒と職員を除く
        $target_users = $target_users->reject(function($item){
            return $item->id == 888 || $item->id == 890;
        });
        foreach($target_users as $user){
            $agreement = new Agreement;
            $member_setting = $user->calendar_member_settings()->first();
            $new_agreement = $agreement->add_from_member_setting($member_setting->id);
            $new_agreement->remark = "Agreement of ".date('Y-m')." from batch.";
            $new_agreement->save();
        }

        echo "Agreements were created successfull.";
      });
    }
}
