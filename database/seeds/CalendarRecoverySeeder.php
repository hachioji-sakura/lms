<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
          $res = $controller->call_api($request, $del_url, "POST", ["id" => $data["id"], "updateuser" => "1"]);
        }
      }
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
        $_sql .= " and m.user_id in(select user_id from common.students)";
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
        $_sql = $sql." where u.exchanged_calendar_id=0 and o.altsched_id>0";
        $d = DB::select($_sql);
        $this->exchanged_calendar_id_clear($d);

        //振替idをつけた場合の同期
        $_sql = $sql." where u.exchanged_calendar_id>0 and o.altsched_id=0";
        $d = DB::select($_sql);
        $this->exchanged_calendar_id_set($d);

        //2020.4-2020.5の振替期限は、2020-12-31
        $this->update_exchange_limit_date_20201231();
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
      $members = UserCalendarMember::with('calendar')->where('schedule_id', '<', 1)->whereRaw('calendar_id in (select id from lms.user_calendars where work not in (11,12))',[])->get();
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
}
