<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendar;
use App\Models\Holiday;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CalendarSettingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendarsetting:make {--range_month=} {--start_date=} {--end_date=} {--week_count=} {--id=} {{--view_mode=}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UserCalendarSettings To UserCalendar Data';

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
      Auth::loginUsingId(1);

      $view_mode = false;
      if(!empty($this->option("view_mode"))){
        $view_mode = true;
      }
      Holiday::holiday_update(); //休日を更新しておく
      $this->to_calendar($this->option("start_date"), $this->option("end_date"), $this->option("range_month"), $this->option("week_count"), $this->option("id"), $view_mode);
    }
    public function to_calendar($start_date='', $end_date='', $range_month=1, $week_count=5, $id=0, $view_mode=false)
    {

      //パラメータ指定がない場合
      if(empty($start_date)) $start_date=date('Y-m-1', strtotime('+1 month'));
      if(empty($end_date)) $end_date=date('Y-m-t', strtotime('+1 month'));
      if(empty($week_count)) $week_count=5;
      if(empty($range_month)) $range_month=1;

      $this->info('to_calendar('.$start_date.','.$end_date.','.$range_month.','.$week_count.','.$id.')');
      @$this->send_slack("calendarsetting:to_calendar:start_date=".$start_date.":range_month=".$range_month.":end_date=".$end_date, 'warning', "CalendarSettingCommand");
      $settings = UserCalendarSetting::where('status', 'fix');
      if(!empty($id)){
        $settings = $settings->where('id', $id);
      }
      $settings = $settings->get();
      if(!isset($settings)){
        $this->info('no settings');
        return false;
      }
      $res = $this->transaction(function() use ($settings,$start_date,$end_date,$range_month, $week_count, $view_mode){
        foreach($settings as $setting){
          $dates = $setting->get_add_calendar_date($start_date, $end_date, $range_month, $week_count);
          $this->info($setting->id.':count='.count($dates));
          $s = null;
          $e = null;
          if(!empty($settng->enable_start_date))$s = strtotime($settng->enable_start_date);
          if(!empty($settng->enable_end_date)) $e = strtotime($settng->enable_end_date);
          foreach($dates as $date => $val){
            $d = strtotime($date);
            $this->info($d.':'.$s.':'.$e);
            if(empty($date)) continue;
            if($e!=null && $e < $d) continue; //設定終了済み
            if($s!=null && $s > $d) continue; //設定開始前
            $this->info('add_calendar / setting_id='.$setting->id.' / date='.$date);
            if($view_mode==false){
              $result = $setting->add_calendar(date('Y-m-d', strtotime($date)));
              if(!$this->is_success_response($result)){
                $this->info($result["message"]."\n".$result["description"]);
                if($result['message'] != 'already_registered' && $result['message'] != 'unsubscribe'){
                  $this->info("繰り返しスケジュール登録エラー:\n".$result["message"]."\n".$result["description"]);
                  @$this->send_slack("繰り返しスケジュール登録エラー:setting_id=".$setting->id."date=".date('Y-m-d', strtotime($date))."\n".$result["message"]."\n".$result["description"], 'error', "CalendarSettingCommand");
                  //return $this->api_response(500, '繰り返しスケジュール登録エラー', $result["message"]."\n".$result["description"], $settings);
                }
              }
            }
          }
        }
        return $this->api_response(200, '', '', $settings);
      }, '繰り返しスケジュール登録', __FILE__, __FUNCTION__, __LINE__ );

      @$this->send_slack("calendarsetting:to_calendar:end", 'warning', "CalendarSettingCommand");
      return true;
    }
    protected function api_response($status=200, $message="", $description="", $data=[]) {
      $controller = new Controller;
      $res = $controller->api_response($status, $message, $description, $data);
      return $res;
    }
    protected function is_success_response($json) {
      $controller = new Controller;
      $res = $controller->is_success_response($json);
      return $res;
    }
    protected function send_slack($message, $msg_type, $username=null, $channel=null) {
      $controller = new Controller;
      $res = $controller->send_slack($message, $msg_type, $username, $channel);
      return $res;
    }
    protected function transaction($callback, $logic_name, $__file, $__function, $__line){
      $controller = new Controller;
      $res = $controller->transaction(null, $callback, $logic_name, $__file, $__function, $__line);
      return $res;
    }
}
