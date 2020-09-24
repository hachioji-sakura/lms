<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\UserCalendar;
use App\Models\Maillog;
use App\Http\Controllers\Controller;

class RemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'command:name';
    //protected $signature = 'name';
    protected $signature = 'remind:trial';
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
      Auth::loginUsingId(1);
      $this->remind_trial_calendar();
      Maillog::all_send();
    }

    private function remind_calendar($calendar, $title, $send_schedule){
      $teacher1 = Teacher::where('id', 1)->first();

      $res = $teacher1->user->remind_mail($title.'確認', [
        'calendar' => $calendar,
        'login_user' => $teacher1->user->details(),
      ], 'text', 'trial_calendar_remind', $send_schedule);
      if($res['status'] == 200){
        $this->info("--remind[id=".$calendar->id."][".$calendar->start_time."][".$title."][".$send_schedule."]--");
        @$this->send_slack("--remind[id=".$calendar->id."][".$calendar->start_time."][".$title."][".$send_schedule."]--", 'warning', "remind_trial_calendar");
      }
      else {
        $this->info("--remind[status=".$res['message']."]");
      }
    }
    protected function send_slack($message, $msg_type, $username=null, $channel=null) {
      $controller = new Controller;
      $res = $controller->send_slack($message, $msg_type, $username, $channel);
      return $res;
    }
    public function remind_trial_calendar(){
      $from = date('Y-m-d 00:00:00');
      //本日の予定以降の体験授業を取得
      $calendars = UserCalendar::rangeDate($from)->where('trial_id' , '>', 0)->findStatuses(['fix'])->get();
      \Log::warning("remind_trial_calendar::count=".count($calendars));
      $this->info("remind_trial_calendar::count=".count($calendars));
      @$this->send_slack("remind_trial_calendar::count=".count($calendars), 'warning', "remind_trial_calendar");

      foreach($calendars as $calendar){
        $title = '【体験授業予定リマインド】';
        $calendar = $calendar->details();
        $title .= $calendar['datetime'].'/'.$calendar['lesson'].'/'.$calendar['course'];
        $title .= $calendar['teacher_name'].'/'.$calendar['student_name'];
        $hour0 = strtotime($calendar->start_time);
        $hour1 = strtotime('-1 hour '.$calendar->start_time);
        $hour3 = strtotime('-3 hour '.$calendar->start_time);
        $hour9 = strtotime('-9 hour '.$calendar->start_time);
        $hour24 = strtotime('-1 day '.$calendar->start_time);
        $this->remind_calendar($calendar, $title.'（1時間前）', date('Y-m-d H:i:s', $hour1));
        $this->remind_calendar($calendar, $title.'（3時間前）', date('Y-m-d H:i:s', $hour3));
        $this->remind_calendar($calendar, $title.'（9時間前）', date('Y-m-d H:i:s', $hour9));
        $this->remind_calendar($calendar, $title.'（明日）', date('Y-m-d 00:00:00', $hour24));
      }
      $this->info("remind_trial_calendar end");
    }
}
