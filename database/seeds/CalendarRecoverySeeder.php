<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarTag;
use App\Models\Student;
use App\Models\Teacher;
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
      $this->set_rest_judgement();

      $this->delete_calendar_sync();
      $this->post_calendar_sync();
      $this->put_calendar_sync();
      $this->season_schedule_lesson_sync();

    }
    public function delete_calendar_sync(){
      $this->no_relate_onetime_schedule_delete();
    }
    public function no_relate_onetime_schedule_delete(){
      //work_id = 10 , 11以外の、user_calendar_membersに紐づいていない、onetime_scheduleを削除
      $sql = <<<EOT
        select
         o.id
        from
        hachiojisakura_calendar.tbl_schedule_onetime o
        left outer join user_calendar_members m on m.schedule_id = o.id
        left outer join user_calendars u on u.id = m.calendar_id
        where o.delflag=0 and m.schedule_id is null
        and o.work_id not in(10, 11)
EOT;
        $_sql = $sql."";
        $d = DB::select($_sql);
        $delete_ids = [];
        foreach($d as $row){
          $delete_ids[] = $row->id;
        }
        if(count($delete_ids)>0){
          DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->whereIn('id', $delete_ids)->update([
            'delflag' => '1'
          ]);
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
        , u.trial_id as u_trial_id
        , u.user_id as u_user_id
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
        , o.trial_id as o_trial_id
        , o.cancel_reason
        , o.user_id as o_user_id
        , o.teacher_id as o_teacher_id
        , o.student_no as o_student_no
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

        //trial_idの同期
        $_sql = $sql." where u.trial_id > 0 and o.trial_id!=u.trial_id ";
        $d = DB::select($_sql);
        $this->trial_id_sync($d);

        //2020.4-2020.5の振替期限は、2020-12-31
        $this->update_exchange_limit_date_20201231();
        //2021.06.03 体験授業で振替期限がついているものをクリアする
        $this->update_exchange_limit_date_clear();

        //schedule_onetime.subject_exprの補完 work=3（面談）,4（試験監督）,9（事務作業）以外
        $_sql = $sql." where o.subject_expr='0' and o.delflag!=1 and u.work not in(3,4,9,10,11)";
        $d = DB::select($_sql);
        $this->subject_expr_sync($d);

        //aステータスの同期(事務の休み)
        $_sql = $sql." where u.work not in (9) and (o.user_id=0 or o.teacher_id = 0 or o.student_no=0)";
        $d = DB::select($_sql);
        $this->update_schedule_ontime_for_user_id($d);

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
    public function update_schedule_ontime_for_user_id($d){
      $id = [];
      foreach($d as $row){
        if(!isset($row->schedule_onetime_id)) continue;
        \Log::warning("update_schedule_ontime_for_user_id:".$row->schedule_onetime_id);
        $student_no = 0;
        $user_id = 0;
        $teacher_id = 0;
        if(isset($row->u_user_id)){
          $t = Teacher::where('user_id', $row->u_user_id)->first();
          if(isset($t)){
            $teacher_id = $t->get_tag_value('teacher_no');
            $user_id = $teacher_id;
          }
        }
        if(isset($row->user_id)){
          $s = Student::where('user_id', $row->user_id)->first();
          if(isset($s)){
            $student_no = $s->get_tag_value('student_no');
            $user_id = $student_no;
          }
        }
        DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->where('id', $row->schedule_onetime_id)->update([
          'user_id' => $user_id,
          'teacher_id' => $teacher_id,
          'student_no' => $student_no
        ]);
      }
    }

    public function update_exchange_limit_date_20201231(){
      DB::table('lms.user_calendar_members')->where('exchange_limit_date','>','2020-03-26')
                                            ->where('exchange_limit_date','<','2021-01-01')
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
    public function trial_id_sync($d){
      $id = [];
      foreach($d as $row){
        DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->where('id', $row->schedule_onetime_id)->update([
          'trial_id' => $row->u_trial_id
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
    public function season_schedule_lesson_sync(){
      $calendars = UserCalendar::whereIn('work',[10,11])->get();
      foreach($calendars as $calendar){
        if(!$calendar->has_tag('lesson', 1)){
          UserCalendarTag::setTag($calendar->id, 'lesson', 1, 1);
        }
        if(!$calendar->has_tag('course_type', 'single')){
          UserCalendarTag::setTag($calendar->id, 'course_type', 'single', 1);
        }
      }
    }
    public function set_rest_judgement(){
      $controller = new Controller;

      $members = UserCalendarMember::with('calendar')->where('schedule_id', '>', 0)
                  ->where('status', 'rest')->whereNull('rest_type')->whereNotNull('rest_contact_date')->get();
      foreach($members as $member){
        if($member->user_id==$member->calendar_user_id) continue;
        $res = null;
        switch($member->calendar->work){
          case 3:
          case 4:
          case 5:
          case 6:
          case 7:
          case 8:
            if($member->user_id != $member->calendar->user_id){
              $res = $member->_rest_judgement();
            }
            break;
          default:
            $res = $member->_rest_judgement();
            break;
        }
        if($res==null) continue;
        if($res['exchange_limit_date']==$member->exchange_limit_date &&
            $res['rest_type']==$member->rest_type &&
            $res['rest_result']==$member->rest_result){
              continue;
        }
        $member->update(['rest_type' => $res['rest_type'],'rest_result' => $res['rest_result'],'exchange_limit_date' => $res['exchange_limit_date']]);
        DB::table('hachiojisakura_calendar.tbl_schedule_onetime')->where('id', $member->schedule_id)->update([
          'cancel' => $res['rest_type'],
          'cancel_reason' => $res['rest_result']
        ]);
      }
    }
    public function update_exchange_limit_date_clear(){
      DB::table('lms.user_calendar_members')->where('exchange_limit_date','!=', null)
                                            ->whereRaw("calendar_id in (select id from user_calendars where trial_id>0)", [])
                                            ->update([
                                              'exchange_limit_date' => null
                                            ]);
      
    }

  }
