<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarTag;
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
      set_time_limit(3600);
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
      $this->put_calendar_sync();
    }
    public function delete_calendar_sync($d){

      $controller = new Controller;
      $request = new Illuminate\Http\Request;

      $del_url = config('app.management_url').'/sakura-api/api_delete_onetime_schedule.php';
      //schedule_idを持つ、memberがない　→　onetime側も削除
      foreach($d as $data){
        $member = UserCalendarMember::where('schedule_id' , $data["id"])->first();
        if(!isset($member)){
          //季節講習以外は、SONが正として、同期をとる（この場合、連携元がなくなったので削除
          $res = $controller->call_api($request, $del_url, "POST", ["id" => $data["id"], "updateuser" => "1"]);
        }
      }
      //季節講習の取り込みのため一度、すべて削除
      $season_calendars = UserCalendar::whereIn('work', [10, 11])->get();
      $ids = [];
      foreach($season_calendars as $season_calendar){
        $ids[] = $season_calendar->id;
      }
      UserCalendarMember::whereIn('calendar_id', $ids)->delete();
      UserCalendarTag::whereIn('calendar_id', $ids)->delete();
      UserCalendar::whereIn('id', $ids)->delete();

      $url = config('app.url').'/import/schedules?work_id=10';
      $res = $controller->call_api($request, $url, 'POST');
      $url = config('app.url').'/import/schedules?work_id=11';
      $res = $controller->call_api($request, $url, 'POST');
    }
    public function put_calendar_sync(){
      $sql = <<<EOT
        select
        concat(t1.id, ':', t1.name_last, t1.name_first) as t_name
        , concat(t2.id, ':', t2.name_last, t2.name_first) as m_name
        , u.id as calendar_id
        , u.start_time
        , u.end_time
        , u.status
        , u.work
        , u.exchanged_calendar_id
        , (select schedule_id from user_calendar_members a where a.calendar_id = u.exchanged_calendar_id limit 1) as exchanged_schedule_id
        , m.id as member_id
        , m.user_id
        , m.status
        , m.rest_type
        , m.rest_result
        , m.exchange_limit_date
        , o.id as schedule_onetime_id
        , o.confirm
        , o.cancel
        , o.cancel_reason
        from user_calendars u
        inner join user_calendar_members m  on u.id = m.calendar_id
        left outer join hachiojisakura_calendar.tbl_schedule_onetime o  on m.schedule_id = o.id
        left outer join common.teachers t1 on t1.user_id = u.user_id
        left outer join common.managers t2 on t2.user_id = u.user_id
EOT;
        //出席ステータスの同期
        $_sql = $sql." where u.status='presence' and m.status='presence' and o.confirm!='f'";
        $d = DB::select($_sql);
        $this->update_schedule_ontime('f', '', $d);

        //キャンセルステータスの同期
        $_sql = $sql." where u.status='cancel' and m.status='cancel' and o.cancel!='c'";
        $d = DB::select($_sql);
        $this->update_schedule_ontime('', 'c', $d);

        //a1ステータスの同期
        $_sql = $sql." where m.status='rest' and m.rest_type='a1' and o.cancel!='a1'";
        $_sql .= " and m.user_id in(select user_id from common.students)";
        $d = DB::select($_sql);
        $this->update_schedule_ontime('', 'a1', $d);

        //a2ステータスの同期
        $_sql = $sql." where m.status='rest' and m.rest_type='a2' and o.cancel!='a2'";
        $_sql .= " and m.user_id in(select user_id from common.students)";
        $d = DB::select($_sql);
        $this->update_schedule_ontime('', 'a2', $d);

        //aステータスの同期(事務の休み)
        $_sql = $sql." where u.status='rest' and u.work=9 and o.cancel!='a'";
        $d = DB::select($_sql);
        $this->update_schedule_ontime('', 'a', $d);

        //振替idを取った場合の同期
        $_sql = $sql." where u.exchanged_calendar_id=0 and o.altsched_id>0 and u.work = 6";
        $d = DB::select($_sql);
        $this->exchanged_calendar_id_clear($d);

        //振替idをつけた場合の同期
        $_sql = $sql." where u.exchanged_calendar_id>0 and o.altsched_id=0 and u.work = 6";
        $d = DB::select($_sql);
        $this->exchanged_calendar_id_set($d);

        //2020.4-2020.5の振替期限は、2020-12-31
        $this->update_exchange_limit_date_20201231();

        //schedule_onetime.subject_exprの補完 work=3（面談）,4（試験監督）,9（事務作業）以外
        $_sql = $sql." where o.subject_expr='0' and o.delflag!=1 and u.work not in(3,4,9,10,11)";
        $d = DB::select($_sql);
        $this->subject_expr_sync($d);
        //季節講習演習の担当講師は、同日・同場所の講師
        $_sql = $sql." where u.work=11";
        $d = DB::select($_sql);
        $this->lesson_place_setup($d);
    }
    public function update_schedule_ontime($confirm, $cancel, $d){
      $id = [];
      foreach($d as $row){
        $id[] = $row->schedule_onetime_id;
      }
      if(count($id)==0) return;
      DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->whereIn('id', $id)->update([
        'confirm' => $confirm,
        'cancel' => $cancel,
      ]);

    }
    public function update_exchange_limit_date_20201231(){
      DB::table('lms.user_calendar_members')->where('exchange_limit_date','>','2020-03-26')
                                            ->whereRaw("calendar_id in (select id from user_calendars where start_time between '2020-04-01 00:00:00' and '2020-06-01 00:00:00')", [])
                                            ->update([
                                              'exchange_limit_date' => '2020-12-31'
                                            ]);
    }
    public function lesson_place_setup($d){
      foreach($d as $row){
        $season_training = UserCalendar::where('id', $row->calendar_id)->first();
        $from = date('Y-m-d 00:00:00', strtotime($season_training->start_time));
        $to = date('Y-m-d 23:59:59', strtotime($season_training->start_time));
        foreach($season_training->members as $member){
          //演習の予定と同日・同場所の講習の予定を取得
          $season_lesson = UserCalendar::where('user_id', $member->user_id)
                            ->rangeDate($from,$to)->where('work', 10)
                            ->where('place_floor_id', $season_training->place_floor_id)->first();
          if(!isset($season_lesson)){
            //予定がない場合は、担当ではない
            $member->delete();
          }
        }
      }
    }
    public function exchanged_calendar_id_clear($d){
      $id = [];
      foreach($d as $row){
        $id[] = $row->schedule_onetime_id;
      }
      if(count($id)==0) return;
      DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->whereIn('id', $id)->update([
        'altsched_id' => 0
      ]);
    }
    public function exchanged_calendar_id_set($d){
      $id = [];
      foreach($d as $row){
        DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->where('id', $row->schedule_onetime_id)->update([
          'altsched_id' => $row->exchanged_schedule_id
        ]);
      }
    }
    public function post_calendar_sync(){
      $controller = new Controller;
      $message = "";
      $members = UserCalendarMember::with('calendar')->where('schedule_id', '<', 1)->whereRaw('calendar_id in (select id from lms.user_calendars where work not in (10, 11))',[])->get();
      foreach($members as $member){
        switch($member->calendar->work){
          case 3:
          case 4:
          case 5:
          case 6:
          case 7:
          case 8:
            if($member->user_id != $member->calendar->user_id){
              $res = $member->office_system_api('POST');
            }
            break;
          default:
            $res = $member->office_system_api('POST');
            break;
        }
      }

    }
    public function subject_expr_sync($d){
      $updated_id = [];
      foreach($d as $row){
        if(isset($updated_id[$row->schedule_onetime_id]) && $updated_id[$row->schedule_onetime_id]===true) continue;
        $calendar = UserCalendar::where('id', $row->calendar_id)->first();
        DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->where('id', $row->schedule_onetime_id)->update([
          'subject_expr' => implode (',', $calendar->subject())
        ]);
        $updated_id[$row->schedule_onetime_id] = true;
      }
    }

}
