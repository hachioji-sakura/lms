<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\UserCalendar;

class RemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'command:name';
    //protected $signature = 'name';
    protected $signature = 'remind:trial {type}';
    //protected $signature = 'sample:sample {name} {age}';
    public $teacher1 = null;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UserCalendar Remind Mail';

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
      $type = $this->argument("type");
      $this->teacher1 = Teacher::where('id', 1)->first();
      switch($type){
        case "today":
          $this->remind_trial_calendar('today');
          break;
        case "tomorrow":
          $this->remind_trial_calendar('tomorrow');
          break;
        default:
          $this->info("command not found");
      }
    }

    public function remind_trial_calendar($type){
      $from = date('Y-m-d 00:00:00');
      $to = date('Y-m-d 23:59:59');
      if($type=='tomorrow'){
        $from = date('Y-m-d 00:00:00', strtotime('+1 day'));
        $to = date('Y-m-d 00:00:00', strtotime('+1 day'));
      }
      //本日の予定を取得
      $calendars = UserCalendar::rangeDate($from, $to)
      ->findStatuses(['fix'])->get();

      \Log::warning("remind_trial_calendar:type=".$type.":count=".count($calendars));
      $this->info("remind_trial_calendar:type=".$type.":count=".count($calendars));
      $now = strtotime('now');
      foreach($calendars as $calendar){
        $title = '体験授業予定';
        if($calendar->is_trial()==false) continue;
        if($type!='tomorrow'){
          $hour0 = strtotime($calendar->start_time);
          $hour1 = strtotime('-1 hour '.$calendar->start_time);
          $hour2 = strtotime('-2 hour '.$calendar->start_time);
          $hour3 = strtotime('-3 hour '.$calendar->start_time);
          $hour8 = strtotime('-8 hour '.$calendar->start_time);
          $hour9 = strtotime('-9 hour '.$calendar->start_time);
          if($now > $hour1 && $now < $hour0){
            //1時間前リマインド
            $is_remind = true;
            $title.= '（1時間前）';
          }
          else if($now > strtotime($hour3) && $now < $hour2){
            //３時間前リマインド
            $is_remind = true;
            $title.= '（3時間前）';
          }
          else if($now > strtotime($hour9) && $now < $hour8){
            //９時間前リマインド
            $is_remind = true;
            $title.= '（9時間前）';
          }
        }
        else {
          $is_remind = true;
          $title.= '（明日）';
        }
        if($is_remind==false) continue;
        $this->remind_calendar($calendar, $title);
      }
      $this->info("remind_trial_calendar end");
    }
    private function remind_calendar($calendar, $title){
      $this->info("--remind[id=".$calendar->id."][".$calendar->start_time."][".$title."]--");
      $this->teacher1->user->send_mail($title.'確認', [
        'calendar' => $calendar,
        'login_user' => $this->teacher1->user->details(),
      ], 'text', 'trial_calendar_remind');
    }
    protected function send_slack($message, $msg_type, $username=null, $channel=null) {
      $controller = new Controller;
      $res = $controller->send_slack($message, $msg_type, $username, $channel);
      return $res;
    }
}
