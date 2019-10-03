<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendar;

class CalendarSettingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendarsetting:make {--range=} {--start_date=} {--week_count=} {--id=}';

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
      $this->to_calendar($this->option("start_date"), $this->option("range"), $this->option("week_count"), $this->option("id"));
    }
    public function to_calendar($start_date='', $range=1, $week_count=5, $id=0)
    {
      $this->info('to_calendar('.$start_date.','.$range.','.$week_count.','.$id.')');
      if(empty($range)) $range=1;
      if(empty($week_count)) $week_count=5;

      $settings = UserCalendarSetting::enable();
      if(!empty($id)){
        $settings = $settings->where('id', $id);
      }
      $settings = $settings->get();
      if(!isset($settings)){
        $this->info('no settings');
        return false;
      }
      $data = [];
      foreach($settings as $setting){
        $schedules = $setting->get_add_calendar_date($start_date, $range, $week_count);
        foreach($schedules as $date => $already_calendar){
          if(isset($already_calendar) && count($already_calendar)>0){
            //作成済みの場合
            continue;
          }
          $this->info('to_calendar:'.$date);
          $ret = $this->_to_calendar($date, $setting);
          if($ret!=null) $data[] = $ret;
        }
      }
      return false;
    }
    private function _to_calendar($date, $setting){
      //担当講師が本登録でない場合、登録できない
      if($setting->user->status!='regular') return null;
      if($setting->is_enable($date)==false) return null;
      $this->info('--------------------------------');
      $this->info('setting:id='.$setting->id);
      $this->info('setting:enable_date='.$setting->enable_date());
      $start_time = $date.' '.$setting->from_time_slot;
      $end_time = $date.' '.$setting->to_time_slot;
      $calendars = UserCalendar::rangeDate($start_time, $end_time)
      ->where('user_id', $setting->user_id)
        ->get();
      $default_status = 'fix';
      if(isset($calendars)){
        //通常授業設定と競合するカレンダーが存在
        $default_status = 'new';
        foreach($calendars as $c){
          if($c->lecture_id == $setting->lecture_id && $c->work == $setting->work && $c->place_floor_id == $setting->place_floor_id){
            $is_same_calendar = true;
            foreach($setting->members as $member){
              if($member->user->details()->status != 'regular') continue;
              if($c->is_member($member->user_id)==false){
                $is_same_calendar = false;
                break;
              }
            }
            if($is_same_calendar==true){
              $this->info('already same calendar exist');
              return null;
            }
          }
        }
      }


      $form = [
        'status' => $default_status,
        'user_calendar_setting_id' => $setting->id,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'lecture_id' => $setting->lecture_id,
        'place_floor_id' => $setting->place_floor_id,
        'work' => $setting->work,
        'exchanged_calendar_id' => 0,
        'remark' => $setting->remark,
        'teacher_user_id' => $setting->user_id,
        'create_user_id' => 1,
      ];
      $message = '';
      foreach($form as $key=>$val){
        $this->info($key.'='.$val);
      }
      $start_date = $date;
      $is_enable = false;

      foreach($setting->members as $member){
        $this->info('user_id='.$member->user_id);
        if($setting->user_id == $member->user_id) continue;
        if($member->user->details()->status != 'regular') continue;
        $is_enable = true;
        break;
      }
      if($is_enable==false){
        //有効なメンバーがいない
        return null;
      }
      $calendar = UserCalendar::add($form);

      foreach($setting->members as $member){
        if($setting->user_id == $member->user_id) continue;
        if(strtotime($member->user->created_at) > strtotime($date)) continue;
        if($member->user->details()->status != 'regular') continue;
        //主催者以外を追加
        $calendar->memberAdd($member->user_id, 1, $default_status);
      }
      return $calendar;
    }

}
