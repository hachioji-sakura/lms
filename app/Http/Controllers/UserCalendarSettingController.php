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
    public function show_fields($item){
      $base_ret = [
        'title' => [
          'label' => __('labels.title'),
        ],
        'repeat_setting_name' => [
          'label' => __('labels.repeat'),
          'size' => 6,
        ],
        'place_floor_name' => [
          'label' => __('labels.place'),
          'size' => 6,
        ],
      ];

      if($item->work==9){
        $ret = [
          'user_name' => [
            'label' => __('labels.charge_user'),
            'size' => 6,
          ],
        ];
      }
      else if($item->is_management()==true){
        $ret = [
          'student_name' => [
            'label' => __('labels.students'),
            'size' => 6,
          ],
          'user_name' => [
            'label' => __('labels.charge_user'),
            'size' => 6,
          ],
          'work_name' => [
            'label' => __('labels.schedule_details'),
            'size' => 6,
          ],
        ];
      }
      else {
        $ret = [
          'student_name' => [
            'label' => __('labels.students'),
            'size' => 6,
          ],
          'user_name' => [
            'label' => __('labels.teachers'),
            'size' => 6,
          ],
          'subject' => [
            'label' => __('labels.subject'),
            'size' => 12,
          ],
          'enable_date' => [
            'label' => '設定有効期間',
            'size' => 12,
          ],
        ];
      }
      $ret['remark'] = [
        'label' => __('labels.remark'),
        'size' => 12,
      ];
      $ret = array_merge($base_ret, $ret);
      return $ret;
    }
    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = $request->all();
      $form['create_user_id'] = $user->user_id;
      $schedule_type = "";
      if($request->has('schedule_type')){
        $schedule_type = $request->get('schedule_type');
      }

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
      if($request->has('schedule_method') && $request->has('lesson_week')){
        $form['schedule_method'] = $request->get('schedule_method');
        $form['lesson_week'] = $request->get('lesson_week');
      }
      if($request->has('start_hours') && $request->has('start_minutes')){
        $form['start_hours'] = $request->get('start_hours');
        $form['start_minutes'] = $request->get('start_minutes');
        $form['from_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['start_hours'].':'.$form['start_minutes']));
      }
      if($request->has('course_minutes')){
        $form['course_minutes'] = $request->get('course_minutes');
        if($schedule_type=='class') $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['from_time_slot'].' +'.$form['course_minutes'].' minutes'));
      }
      else if($request->has('end_hours') && $request->has('end_minutes')){
        if($schedule_type!='class') $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['end_hours'].':'.$form['end_minutes']));
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
        $teacher = Teacher::where('id', $form['teacher_id'])->first();
        if(!isset($teacher)){
          //講師が存在しない
          abort(400, "存在しない講師");
        }
        $form['user_id'] = $teacher->user_id;
      }

      //生徒の指定
      if($request->has('student_id')){
        $form['student_id'] = $request->get('student_id');
        $form['students'] = [];
        foreach($form['student_id'] as $student_id){
          $student = Student::where('id', $student_id)->first();
          if(!isset($student)){
            //生徒が存在しない
            abort(400, "存在しない生徒");
          }
          $form['students'][] = $student;
        }
      }

      return $form;
    }
    public function update_form(Request $request, $id=0){
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
      if($request->has('schedule_method') && $request->has('lesson_week')){
        $form['schedule_method'] = $request->get('schedule_method');
        $form['lesson_week'] = $request->get('lesson_week');
      }
      if($request->has('start_hours') && $request->has('start_minutes')){
        $form['start_hours'] = $request->get('start_hours');
        $form['start_minutes'] = $request->get('start_minutes');
        $form['from_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['start_hours'].':'.$form['start_minutes']));
      }
      if($request->has('course_minutes')){
        $form['course_minutes'] = $request->get('course_minutes');
        $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['from_time_slot'].' +'.$form['course_minutes'].' minutes'));
      }
      if($request->has('end_hours') && $request->has('end_minutes')){
        $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['end_hours'].':'.$form['end_minutes']));
      }
      $form['charge_subject'] = $request->get('charge_subject');
      $form['english_talk_lesson'] = $request->get('english_talk_lesson');
      $form['piano_lesson'] = $request->get('piano_lesson');
      $form['kids_lesson'] = $request->get('kids_lesson');
      $form['lesson'] = $request->get('lesson');
      $form['course_type'] = $request->get('course_type');
      $form['place'] = $request->get('place');

      if($request->has('teacher_id')){
        //講師の指定
        $form['teacher_id'] = $request->get('teacher_id');
        $teacher = Teacher::where('id', $form['teacher_id'])->first();
        if(!isset($teacher)){
          //講師が存在しない
          abort(400, "存在しない講師");
        }
        $form['user_id'] = $teacher->user_id;
      }

      if($request->has('student_id')){
        //生徒の指定
        $form['student_id'] = $request->get('student_id');
        $form['students'] = [];
        foreach($form['student_id'] as $student_id){
          $student = Student::where('id', $student_id)->first();
          if(!isset($student)){
            //生徒が存在しない
            abort(400, "存在しない生徒");
          }
          $form['students'][] = $student;
        }
      }
      return $form;
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

      $items = $items->orderByWeek();
      $items = $this->_search_sort($request, $items);

      $items = $items->get();
      $fields = [
        'title' => [
          'label' => __('labels.title'),
          "link" => "show",
        ],
        "student_name" => [
          "label" => __('labels.students'),
        ],
        'repeat_setting_name' => [
          'label' => __('labels.repeat'),
        ],
        "status_name" => [
          "label" => __('labels.status'),
        ],
        'calendar_count' => [
          'label' => '登録予定数',
          "link" => function($row){
            return "/calendars?user_calendar_setting_id=".$row['id'];
          }
        ],
        "place_floor_name" => [
          "label" => __('labels.place'),
        ],
        "buttons" => [
          "label" => __('labels.control'),
          "button" => [
            "to_calendar" => [
              "method" => "to_calendar",
              "label" => "予定登録",
              "style" => "outline-secondary",
            ],
            "delete_calendar" => [
              "method" => "delete_calendar",
              "label" => "予定削除",
              "style" => "outline-danger",
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
      $ret = $this->get_common_param($request);
      if($request->has('trial_id')){
        $ret['trial_id'] = $request->get('trial_id');
      }

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
      $param['fields'] = $this->show_fields($param['item']);
      return view($this->domain.'.page', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * カレンダー登録画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function to_calendar_page(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']);

      return view($this->domain.'.to_calendar', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * カレンダー登録画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function all_to_calendar_page(Request $request)
    {
      if(!$request->has('teacher_id')){
        abort(400);
      }
      $teacher = Teacher::where('id', $request->get('teacher_id'))->first();
      $user_id = $teacher->user_id;
      if(!isset($teacher)){
        abort(400);
      }

      $param = $this->get_param($request);
      $param['target_user_id'] = $user_id;

      return view($this->domain.'.all_to_calendar', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * カレンダー削除画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_calendar_page(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']);

      return view($this->domain.'.delete_calendar', [
        'action' => $request->get('action')
      ])->with($param);
    }
    /**
     * 設定により登録される日付範囲を取得
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function to_calendar_data(Request $request, $id=0)
    {
      if($id == 0){
        //全設定指定
        if(!$request->has('user_id')){
          //全設定指定の場合、user_idを指定する
          return $this->bad_request("user_id not found");
        }
        $user = User::where('id', $request->get('user_id'))->first();
        if(!isset($user)){
          return $this->bad_request("user not found");
        }
        $settings = $user->calendar_settings;
        $param = $this->get_param($request);
      }
      else {
        $param = $this->get_param($request, $id);
        $settings = [$param['item']];
      }
      if($param['user']->role !== 'manager'){
        return $this->forbidden("This User is not manager role.");
      }
      if(!$request->has('start_date') || !$request->has('end_date')){
        return $this->bad_request();
      }
      $items = [];
      foreach($settings as $setting){
        $items = array_merge($items, $setting->get_add_calendar_date($request->start_date, $request->end_date, 1, 5));
      }
      ksort($items);

      return $this->api_response(200, '', '', $items);
    }
    public function _update(Request $request, $id)
    {
      return $res = $this->transaction($request, function() use ($request, $id){
        $form = $this->update_form($request);
        $param = $this->get_param($request, $id);
        $form['create_user_id'] = $param['user']->user_id;
        $setting = UserCalendarSetting::where('id', $id)->first();
        //生徒をカレンダーメンバーに追加
        if(!empty($form['students'])){
          foreach($form['students'] as $student){
            $already_setting = $setting->members->where('user_id', $student->user_id);
            if(!isset($already_setting)){
              //既存メンバー以外に指定されている場合、追加
              $setting->memberAdd($student->user_id, $form['create_user_id']);
            }
          }
          foreach($setting->members as $member){
            $is_delete = true;
            foreach($form['students'] as $student){
              if($member->user_id == $student->user_id){
                $is_delete = false;
                break;
              }
            }
            if($is_delete == true){
              //既存メンバーが指定されていない場合、削除
              $member->dispose();
            }
          }
        }
        $res = $setting->change($form);
        //TODO 更新失敗しても更新成功のエラーメッセージが表示されてしまう
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
      $res = $this->transaction($request, function() use ($request){
        $form = $this->create_form($request);
        $setting = UserCalendarSetting::add($form);
        if($setting != null){
          //生徒をカレンダーメンバーに追加
          if(!empty($form['students'])){
            foreach($form['students'] as $student){
              $setting->memberAdd($student->user_id, $form['create_user_id']);
            }
          }
          $setting = $setting->details();
          $this->send_slack('追加/ id['.$setting['id'].']生徒['.$setting['student_name'].']講師['.$setting['teacher_name'].']', 'info', '追加');
        }
        return $setting;
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );

      return $res;
    }
    public function _delete(Request $request, $id)
    {
      return $res = $this->transaction($request, function() use ($request, $id){
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
          $setting->dispose();
        }
        return $setting;
      }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
    }
    public function to_calendar(Request $request, $id)
    {
      $res = null;
      $param = $this->get_param($request, $id);
      if($param['user']->role !== 'manager'){
        $res = $this->forbidden();
      }
      if(!$request->has('select_dates')){
        $res = $this->bad_request();
      }
      $setting = $param['item'];
      $res = $this->transaction($request, function() use ($request, $setting){
        $form['select_dates'] = $request->get('select_dates');
        foreach($request->get('select_dates') as $date){
          $setting->add_calendar(date('Y-m-d', strtotime($date)));
        }
        return $setting;
      }, '繰り返しスケジュール登録', __FILE__, __FUNCTION__, __LINE__ );

      return $this->save_redirect($res, $param, '繰り返しスケジュールを登録しました。');
    }
    public function delete_calendar(Request $request, $id)
    {
      $res = null;
      $param = $this->get_param($request, $id);
      if($param['user']->role !== 'manager'){
        $res = $this->forbidden();
      }
      if(!$request->has('select_ids')){
        $res = $this->bad_request();
      }
      $setting = $param['item'];
      $res = $this->transaction($request, function() use ($request, $id, $setting){
        $calendars = UserCalendar::where('user_calendar_setting_id', $id)
                    ->whereIn('id', $request->get('select_ids'))->get();
        foreach($calendars as $calendar){
          $calendar->dispose();
        }
        return $setting;
      }, 'スケジュール一括削除', __FILE__, __FUNCTION__, __LINE__ );

      return $this->save_redirect($res, $param, '削除しました。');
    }
    /**
     * 新規登録画面
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Request $request)
   {
      $param = $this->get_param($request);
      //新規
      $param['item'] = new UserCalendarSetting();
      $param['item']->work = "";
      $param['item']->place = "";
      $param['teachers'] = [];
      if($param['user']->role==="teacher"){
        $param['teachers'][] = $param['user'];
        $param['teacher_id'] = $param['user']->id;
      }
      else if($param['user']->role==="manager"){
        if($request->has('teacher_id')){
          $param['teachers'][] = Teacher::where('id', $request->get('teacher_id'))->first();
          $param['teacher_id'] = $request->get('teacher_id');
        }
        else if($request->has('manager_id')){
          //事務からの登録の場合、作業内容＝9 (事務作業）
          $param['item']->work = 9;
        }
      }
      if($param['item']->work!=9 && !isset($param['teacher_id'])) {
        $param["teachers"] = Teacher::findStatuses(["regular"])->get();
        return view('teachers.select_teacher',
          [ 'error_message' => '', '_edit' => false])
          ->with($param);
      }
      $param['item']['course_minutes'] = 0;
      if($request->has('course_minutes')){
        $param['item']['course_minutes'] = $request->get('course_minutes');
      }
      return view($this->domain.'.create',
        [ 'error_message' => '', '_edit' => false])
        ->with($param);
    }
}
