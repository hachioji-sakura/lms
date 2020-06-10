<?php

use Illuminate\Database\Seeder;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Http\Controllers\Controller;
class CalendarRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $controller = new Controller;
      $request = new Illuminate\Http\Request;
      $url = config('app.management_url').'/sakura-api/api_get_onetime_schedule.php';
      $res = $controller->call_api($request, $url);
      $d = [];
      foreach($res["data"] as $data){
        $d[intval($data["id"])] = $data;
      }

      $this->delete_calendar_sync($d);
      $this->post_calendar_sync();
    }
    public function delete_calendar_sync($d){
      $controller = new Controller;
      $request = new Illuminate\Http\Request;

      $del_url = config('app.management_url').'/sakura-api/api_delete_onetime_schedule.php';

      foreach($d as $data){
        $member = UserCalendarMember::where('schedule_id' , $data["id"])->first();
        if(!isset($member)){
          echo "del:".$data["id"];
          $res = $controller->call_api($request, $del_url, "POST", ["id" => $data["id"]]);
        }
      }
    }
    public function put_calendar_sync(){
      $calendars = UserCalendar::rangeDate('2020-04-01', '2020-07-01')->get();
      foreach($calendars as $calendar){
        foreach($calendar->members as $member){
          if($member->schedule_id>0){
            switch($calendar->work){
              case 6:
              case 7:
              case 8:
                $rest_type = "";
                $rest_result="";
                if($calendar->is_rest_status($calendar->status)==true){
                  $rest_type = $member->rest_type;
                  $rest_result = $member->rest_result;
                }
                $res = $member->office_system_api('PUT', $rest_type, $rest_result);
                break;
              default:
                $res = $member->office_system_api('PUT');
                break;
            }
          }
        }
      }
    }
    public function post_calendar_sync(){
      $controller = new Controller;
      $message = "";
      $calendars = UserCalendar::whereNotIn('work' , [11,12])->get();
      foreach($calendars as $calendar){
        $schedule_id = 0;
        foreach($calendar->members as $member){
          if($member->schedule_id > 0){
            $schedule_id = $member->schedule_id;
          }
        }
        if($schedule_id==0){
          echo "calendar_id=".$calendar->id."\n";
          foreach($calendar->members as $member){
            if($member->schedule_id==0){
              switch($calendar->work){
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                  if($member->user_id != $calendar->user_id){
                    $res = $member->office_system_api('POST');
                  }
                  break;
                default:
                  $res = $member->office_system_api('POST');
                  break;
              }
            }
          }
        }
      }
    }
}
