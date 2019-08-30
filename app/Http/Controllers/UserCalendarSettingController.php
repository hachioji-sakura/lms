<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Lecture;
use App\Models\ChargeStudent;
use App\Models\UserTag;
use App\Models\Trial;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;
use DB;
use View;

class UserCalendarSettingController extends UserCalendarController
{
  public $domain = 'calendar_settings';
  public $table = 'user_calendar_settings';

    public function model(){
      return UserCalendarSetting::query();
    }
    public function show_fields($work){
      if($work==9){
        $ret = [
          'title' => [
            'label' => __('labels.title'),
          ],
          'place_floor_name' => [
            'label' => __('labels.place'),
          ],
          'title2' => [
            'label' => __('labels.details'),
          ],
          'user_name' => [
            'label' => __('labels.charge_user'),
            'size' => 6,
          ],
          'enable_date' => [
            'label' => __('labels.subject'),
            'size' => 12,
          ],
        ];
      }
      else {
        $ret = [
          'title' => [
            'label' => __('labels.title'),
          ],
          'place_floor_name' => [
            'label' => __('labels.place'),
            'size' => 6,
          ],
          'student_name' => [
            'label' => __('labels.students'),
            'size' => 6,
          ],
          'user_name' => [
            'label' => __('labels.charge_user'),
            'size' => 6,
          ],
          'subject' => [
            'label' => __('labels.subject'),
            'size' => 12,
          ],
          'enable_date' => [
            'label' => '設定有効日',
            'size' => 12,
          ],
        ];
      }
      return $ret;
    }
    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = $request->all();
      $form['create_user_id'] = $user->user_id;
      if($request->has('lesson_week_count')){
        $form['lesson_week_count'] = $request->get('lesson_week_count');
      }
      if(empty($form['lesson_week_count'])){
        $form['lesson_week_count'] = 0;
      }
      $form['remark'] = "";
      if($request->has('remark')){
        $form['remark'] = $request->get('remark');
      }

      //予定の指定
      if($request->has('schedule_method') && $request->has('lesson_week') && $request->has('start_hours')
          && $request->has('start_minutes') && $request->has('course_minutes')){
        $form['schedule_method'] = $request->get('schedule_method');
        $form['lesson_week'] = $request->get('lesson_week');
        $form['start_hours'] = $request->get('start_hours');
        $form['start_minutes'] = $request->get('start_minutes');
        $form['course_minutes'] = $request->get('course_minutes');
        $form['from_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['start_hours'].':'.$form['start_minutes']));
        $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['from_time_slot'].' +'.$form['course_minutes'].' minutes'));
      }
      $form['charge_subject'] = $request->get('charge_subject');
      $form['english_talk_lesson'] = $request->get('english_talk_lesson');
      $form['piano_lesson'] = $request->get('piano_lesson');
      $form['kids_lesson'] = $request->get('kids_lesson');
      $form['lesson'] = $request->get('lesson');
      $form['course_type'] = $request->get('course_type');
      $form['place'] = $request->get('place');
      //生徒と講師の情報が予定追加時には必須としている
      //講師の指定
      if($request->has('teacher_id')){
        $form['teacher_id'] = $request->get('teacher_id');
      }
      $teacher = Teacher::where('id', $form['teacher_id'])->first();
      if(!isset($teacher)){
        //講師が存在しない
        abort(400, "存在しない講師");
      }
      $form['user_id'] = $teacher->user_id;

      //生徒の指定
      if($request->has('student_id')){
        $form['student_id'] = $request->get('student_id');
      }
      else {
        abort(400, "生徒指定なし");
      }
      $form['students'] = [];
      foreach($form['student_id'] as $student_id){
        $student = Student::where('id', $student_id)->first();
        if(!isset($student)){
          //生徒が存在しない
          abort(400, "存在しない生徒");
        }
        $form['students'][] = $student;
      }

      return $form;
    }
    public function api_setting_to_calendar(Request $request, $id=0){
      $settings = UserCalendarSetting::enable();
      if($id>0){
        $settings = $settings->where('id', $id);
      }
      $settings = $settings->get();
      if(!isset($settings)){
        return $this->notfound();
      }
      $range_month = 1;
      if($request->has("range_month")){
        $range_month = $request->get("range_month");
      }
      $start_date = "";
      if($request->has("start_date")){
        $start_date = $request->get("start_date");
      }
      $month_week_count = 5;
      if($request->has("month_week_count")){
        $month_week_count = $request->get("month_week_count");
      }
      $data = [];
      foreach($settings as $setting){
        $schedules = $setting->get_add_calendar_date($start_date, $range_month, $month_week_count);
        foreach($schedules as $date => $already_calendar){
          if(isset($already_calendar) && count($already_calendar)>0){
            //作成済みの場合
            continue;
          }
          $data[] = $this->_to_calendar($date, $setting);
        }
      }
      return $this->api_response(200, "", "", $data);
    }
    private function _to_calendar($date, $setting){
      //担当講師が本登録でない場合、登録できない
      if($setting->user->status!='regular') return null;

      $start_time = $date.' '.$setting->from_time_slot;
      $end_time = $date.' '.$setting->to_time_slot;
      $c = UserCalendar::rangeDate($start_time, $end_time)
      ->where('user_id', $setting->user_id)
        ->get();
      $default_status = 'fix';
      if(isset($c)){
        //通常授業設定と競合するカレンダーが存在
        $default_status = 'new';
      }
      $form = [
        'status' => $default_status,
        'user_calendar_setting_id' => $setting->id,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'lecture_id' => $setting->lecture_id,
        'place' => $setting->place,
        'work' => $setting->work,
        'exchanged_calendar_id' => 0,
        'remark' => $setting->remark,
        'teacher_user_id' => $setting->user_id,
        'create_user_id' => 1,
      ];
      $start_date = $date;
      $is_enable = false;

      foreach($setting->members as $member){
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
    public function search(Request $request, $user_id=0)
    {
      $items = $this->model();

      //曜日検索
      if(isset($request->search_week)){
        $_param = "";
        if(gettype($request->search_week) == "array") $_param  = $request->search_week;
        else $_param = explode(',', $request->search_week.',');
        $items = $items->findWeeks($_param);
      }

      $items = $this->_search_scope($request, $items);
      $count = $items->count();
      $items = $this->_search_pagenation($request, $items);

      //$items = $items->orderByWeek();
      $items = $this->_search_sort($request, $items);

      $items = $items->get();
      $fields = [
        "id" => [
          "label" => "ID",
          "link" => function($row){
            return "/calendars?setting_id=".$row['id'];
          }
        ],
        "week_setting" => [
          "label" => __('labels.week_day'),
          "link" => "show",
        ],
        "timezone" => [
          "label" => __('labels.timezone'),
        ],
        "place_floor_name" => [
          "label" => __('labels.place'),
        ],
        "work_name" => [
          "label" => __('labels.work'),
        ],
        "user_name" => [
          "label" => __('labels.charge_user'),
        ],
        "student_name" => [
          "label" => __('labels.students'),
        ],
        "subject" => [
          "label" => __('labels.subject'),
        ],
        "buttons" => [
          "label" => __('labels.control'),
          "button" => [
            "to_calendar" => [
              "method" => "to_calendar",
              "label" => "適用",
              "style" => "default",
            ],
            "edit",
            "delete"]
        ]
      ];
      foreach($items as $item){
        $item = $item->details($user_id);
        /*
        if($user_id > 0) {
          $item->own_member = $item->get_member($user_id);
        }
        */
      }
      return ["items" => $items, "fields" => $fields, "count" => $count];
    }
    /**
     * データ更新時のパラメータチェック
     *
     * @return \Illuminate\Http\Response
     */
    public function save_validate(Request $request)
    {
      return $this->api_response(200, '', '');
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)){
        abort(403, 'このページにはアクセスできません(2)');
      }
      if($this->is_manager($user->role)===false && $this->is_teacher($user->role)===false){
        abort(403, 'このページにはアクセスできません(3)');
      }
      //$user = User::where('id', 607)->first()->details();
      $ret = [
        'domain' => $this->domain,
        'domain_name' => __('labels.'.$this->domain),
        'teacher_id' => $request->teacher_id,
        'user' => $user,
        'trial_id' => $request->trial_id,
        '_page' => $request->get('_page'),
        '_line' => $request->get('_line'),
        'attributes' => $this->attributes(),
      ];
      $ret['filter'] = [
        'search_work' => $request->search_work,
        'search_week' => $request->search_week,
        'search_place' => $request->search_place,
      ];

      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id',$id)->first();
        if(!isset($item)){
          abort(404, 'ページがみつかりません(1)');
        }
        if($this->is_teacher($user->role)===true && $user->user_id != $item->user_id){
          abort(403, 'このページにはアクセスできません(4)');
        }

        $ret['item'] = $item->details($user->user_id);
        $ret['select_lesson'] = 0;
        if(!empty($item->get_tag('lesson'))){
          $ret['select_lesson'] = $item->get_tag('lesson')->tag_value;
        }
        if(count($item['teachers'])>0){
          $ret['candidate_teacher'] = $item['teachers'][0]->user->teacher;
          $ret['candidate_teacher']["enable_subject"] = $item['teachers'][0]->user->teacher->get_subject($ret['select_lesson']);
        }
      }
      return $ret;
    }
    /**
     * 詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']->work);
      if($param['item']->is_teaching()===false){
        unset($param['fields']['subject']);
        unset($param['fields']['student_name']);
        unset($param['fields']['title2']);
      }
      return view($this->domain.'.page', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * 詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function to_calendar_page(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']->work);
      $param['add_dates'] = $param['item']->get_add_calendar_date();

      return view($this->domain.'.to_calendar', [
        'action' => $request->get('action')
      ])->with($param);
    }
    public function _update(Request $request, $id)
    {
      return $res = $this->transaction(function() use ($request, $id){
        $form = $this->create_form($request);
        $param = $this->get_param($request, $id);
        $form['create_user_id'] = $param['user']->user_id;
        $setting = UserCalendarSetting::where('id', $id)->first();
        $setting->change($form);
        //生徒をカレンダーメンバーに追加
        if(!empty($form['students'])){
          UserCalendarMemberSetting::where('user_calendar_setting_id', $setting->id)->delete();
          foreach($form['students'] as $student){
            $setting->memberAdd($student->user_id, $form['create_user_id']);
          }
        }

        return $setting;
      }, '更新', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 新規登録
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $param = $this->get_param($request);
      $res = $this->_store($request);

      return $this->save_redirect($res, $param, '登録しました。');
    }
    /**
     * 新規登録ロジック
     *
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
      $res = $this->transaction(function() use ($request){
        $form = $this->create_form($request);

        $setting = UserCalendarSetting::add($form);
        //生徒をカレンダーメンバーに追加
        if(!empty($form['students'])){
          foreach($form['students'] as $student){
            $setting->memberAdd($student->user_id, $form['create_user_id']);
          }
        }
        $setting = $setting->details();
        $this->send_slack('追加/ id['.$setting['id'].']生徒['.$setting['student_name'].']講師['.$setting['teacher_name'].']', 'info', '追加');
        $param = $this->get_param($request, $setting->id);
        return $setting;
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    public function _delete(Request $request, $id)
    {
      return $res = $this->transaction(function() use ($request, $id){
        $setting = UserCalendarSetting::where('id', $id)->first();
        if($setting->is_group()==true){
          $form = $request->all();
          foreach($setting->members as $member){
            if(isset($form[$member->id.'_delete']) && $form[$member->id.'_delete']=='delete'){
              $member->dispose();
            }
          }
        }
        else {
          //$setting->dispose();
        }
        return $setting;
      }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
    }
}
