<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ask;
use App\Models\UserCalendarSetting;
use App\Http\Controllers\Controller;

class DailyProcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyproc:ask {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ask commit to complete';

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
      $date = $this->argument("date");
      $this->daily_proc($date);
      $this->auto_calendar_settings_expired();
    }
    public function daily_proc($d='')
    {
      if(empty($d)) $d = date('Y-m-d');
      $result = ['asks'=>[], 'recess'=>[],'unsubscribe'=>[]];
      //退会・休会申請にて承認済みの依頼を取得
      //開始＝本日（指定日）
      $this->info("daily_proc[d=".$d."]");
      @$this->send_slack("daily_proc[d=".$d."]", 'warning', "daily_proc");
      $asks = Ask::where('status', 'commit')->findTypes(['recess', 'unsubscribe'])
        ->get();
      foreach($asks as $ask){
        //対象のモデルを取得
        $target_model_data = $ask->get_target_model_data();
        if($target_model_data==null) continue;
        $result['asks'][] = $ask;
        $this->info("休会,退会　ask proc[id=".$ask->id."]");
        if($ask->type=="recess"){
          //休会（休職）
          $ret = $target_model_data->recess();
        }
        else if($ask->type=="unsubscribe"){
          //退会（退職）
          $ret = $target_model_data->unsubscribe();
        }

        if($ret!=null){
          //成功した場合
          if(isset($ret['user_calendar_members'])){
            $target_model_data['user_calendar_members'] = $ret['user_calendar_members'];
          }
          $result[$ask->type][] = $target_model_data;
          if(($ask->type=="unsubscribe" && $target_model_data->status=='unsubscribe') ||
              ($ask->type=="recess" && $target_model_data->status=='regular')){
            //退会の場合は、承認済み→完了
            $ask->complete();
            $this->info("success[id=".$ask->id."]");
            @$this->send_slack("success[id=".$ask->id."][type=".$ask->type."]", 'warning', "daily_proc");
          }
        }
        else {
          //失敗
          $this->info("failed[id=".$ask->id."][".$target_model_data->status."]");
          @$this->send_slack("failed[id=".$ask->id."][type=".$ask->type."]", 'warning', "daily_proc");
        }
      }

      return $result;
   }
   public function auto_calendar_settings_expired(){
    $calendar_settings = UserCalendarSetting::where('status', 'fix')->get();
    foreach($calendar_settings as $calendar_setting){
      $calendar_setting->auto_expired();
    }
   }
   protected function send_slack($message, $msg_type, $username=null, $channel=null) {
     $controller = new Controller;
     $res = $controller->send_slack($message, $msg_type, $username, $channel);
     return $res;
   }
}
