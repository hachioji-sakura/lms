<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Lecture;
use App\Models\ChargeStudent;
use App\Models\UserTag;
use App\Models\Trial;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;
use App\Models\Agreement;
use App\Models\AgreementStatement;
use DB;
use View;

class UserCalendarSettingController extends UserCalendarController
{
  public $domain = 'calendar_settings';
  public $table = 'user_calendar_settings';

    public function model(){
      return UserCalendarSetting::query();
    }
    public function page_title($item, $page_status){
      if($item->is_teaching()==true){
        $title = __('labels.regular_class_schedule');
      }
      else {
        $title = $item->work()."(".__('labels.repeat').' '.__('labels.setting').")";
      }
      switch($page_status){
        case "confirm":
          $title.=' '.__('labels.confirm');
          break;
        default:
          $title.= ' '.__('labels.details');
      }
      return $title;
    }
    public function show_fields($item=''){
      $base_ret = [
        'title' => [
          'label' => __('labels.title'),
        ],
        'status_name' => [
          'label' => __('labels.status'),
          'size' => 6,
        ],
      ];
      if($item->is_teaching()==true && !empty($item->enable_start_date)){
        $base_ret['schedule_start_date'] = [
            'label' => __('labels.schedule_start_date'),
            'size' => 6,
        ];
      }
      $base_ret['repeat_setting_name'] = [
        'label' => __('labels.repeat'),
        'size' => 6,
      ];
      $base_ret['place_floor_name'] = [
        'label' => __('labels.place'),
        'size' => 6,
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
          'teacher_name' => [
            'label' => __('labels.teachers'),
            'size' => 12,
          ],
          'student_name' => [
            'label' => __('labels.students'),
            'size' => 6,
          ],
          'subject' => [
            'label' => __('labels.subject'),
            'size' => 12,
          ],
        ];
      }
      $ret = array_merge($base_ret, $ret);
      return $ret;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = $this->login_details($request);
      if(!isset($user)) abort(403);
      if($this->is_manager($user->role)!=true) abort(403);

      if(!$request->has('_origin')){
        $request->merge([
          '_origin' => $this->domain,
        ]);
      }
      if(!$request->has('_line')){
        $request->merge([
          '_line' => $this->pagenation_line,
        ]);
      }
      if(!$request->has('_page')){
        $request->merge([
          '_page' => 1,
        ]);
      }
      else if($request->get('_page')==0){
        $request->merge([
          '_page' => 1,
        ]);
      }
      $sort = 'asc';
      if($request->has('is_desc') && $request->get('is_desc')==1){
        $sort = 'desc';
      }
      $request->merge([
        '_sort' => 'enable_start_date',
        '_sort_order' => $sort,
      ]);

      $param = $this->get_param($request);
      $_table = $this->search($request);
      return view($this->domain.'.lists', $_table)
        ->with($param);
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
      $form['lesson'] = $request->get('lesson');
      switch(intval($form['lesson'])){
        case 1:
          $form['charge_subject'] = $request->get('charge_subject');
          break;
        case 2:
          $form['english_talk_lesson'] = $request->get('english_talk_lesson');
          break;
        case 3:
          $form['piano_lesson'] = $request->get('piano_lesson');
          break;
        case 4:
          $form['kids_lesson'] = $request->get('kids_lesson');
          break;
      }
      $form['place'] = $request->get('place');
      if($request->has('is_online')) $form['is_online'] = $request->get('is_online');

      //生徒と講師の情報が予定追加時には必須としている
      //講師の指定
      if($request->has('teacher_id') && $request->get('teacher_id') > 0){
        $form['teacher_id'] = $request->get('teacher_id');
        $teacher = Teacher::where('id', $form['teacher_id'])->first();
        if(!isset($teacher)){
          //講師が存在しない
          abort(400, "存在しない講師");
        }
        $form['user_id'] = $teacher->user_id;
      }

      //生徒の指定
      if($request->has('student_id') && $request->get('student_id') > 0){
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

      if($request->has('manager_id') && $request->get('manager_id') > 0){
        $form['manager_id'] = $request->get('manager_id');
        $manager = Manager::where('id', $form['manager_id'])->first();
        if(!isset($manager)){
          //講師が存在しない
          abort(400, "存在しない講師");
        }
        $form['user_id'] = $manager->user_id;
        $form['target_user_id'] = $manager->user_id;
      }
      $form['course_type'] = $schedule_type;
      if($schedule_type=='other'){
        $form['work'] = $request->get('work');
      }
      else if($schedule_type=='office_work'){
        $form['work'] = 9;
      }
      else {
        $form['course_type'] = $request->get('course_type');
        unset($form['work']);
      }

      return $form;
    }
    public function update_form(Request $request, $id=0){
      $user = $this->login_details($request);
      $form = $request->all();
      $schedule_type = "";
      if($request->has('schedule_type')){
        $schedule_type = $request->get('schedule_type');
      }

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
        if($schedule_type=='class') $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['from_time_slot'].' +'.$form['course_minutes'].' minutes'));
      }
      if($request->has('end_hours') && $request->has('end_minutes')){
        if($schedule_type!='class') $form['to_time_slot'] = date('H:i:s', strtotime("2000-01-01 ".$form['end_hours'].':'.$form['end_minutes']));
      }
      $form['lesson'] = $request->get('lesson');
      switch(intval($form['lesson'])){
        case 1:
          $form['charge_subject'] = $request->get('charge_subject');
          break;
        case 2:
          $form['english_talk_lesson'] = $request->get('english_talk_lesson');
          break;
        case 3:
          $form['piano_lesson'] = $request->get('piano_lesson');
          break;
        case 4:
          $form['kids_lesson'] = $request->get('kids_lesson');
          break;
      }

      $form['place'] = $request->get('place');
      if($request->has('is_online')) $form['is_online'] = $request->get('is_online');

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
      $param = $this->get_param($request);
      $items = $this->model();

      //曜日検索
      if(isset($request->search_week)){
        $_param = "";
        if(gettype($request->search_week) == "array") $_param  = $request->search_week;
        else $_param = explode(',', $request->search_week.',');
        $items = $items->findWeeks($_param);
      }

      $items = $this->_search_scope($request, $items);
      $items = $items->orderByWeek()->orderBy($request->_sort, $request->_sort_order)->paginate($param['_line']);

      $fields = [
        'repeat_setting_name' => [
          'label' => __('labels.title'),
          "link" => "show",
        ],
        'user_name' => [
          'label' => __('labels.charge_user'),
        ],
        "student_name" => [
          "label" => __('labels.students'),
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
              "style" => "outline-success",
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
      return ["items" => $items, "fields" => $fields];
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
      /*
      通常授業も生徒と確認を取り合うので、アクセスを可能にする
      if($this->is_manager($user->role)===false && $this->is_teacher($user->role)===false){
        abort(403, 'このページにはアクセスできません(3)');
      }
      */
      $ret = $this->get_common_param($request);
      if($request->has('trial_id')){
        $ret['trial_id'] = $request->get('trial_id');
      }

      if(is_numeric($id) && $id > 0){
        $user_id = -1;
        if($request->has('user')){
          $user_id = $request->get('user');
        }

        $item = $this->model()->where('id',$id)->first();
        if(!isset($item)){
          abort(404, 'ページがみつかりません(1)');
        }
        if($this->is_teacher($user->role)===true && $user->user_id != $item->user_id){
          abort(403, 'このページにはアクセスできません(4)');
        }
        if($user_id>0){
          $user = User::where('id', $user_id)->first();
          if(!isset($user)){
            abort(403, '有効期限が切れています(4)');
          }
          $user = $user->details();
          $ret['user'] = $user;
        }
        if(isset($user)){
          if($this->is_manager($user->role)!=true){
            if($item->is_access($user->user_id)!=true){
              abort(403, 'このページにはアクセスできません(1)'.$user->role);
            }
          }
        }
        else {
          abort(403, 'このページにはアクセスできません(2)');
        }

        $ret['item'] = $item->details($user->user_id);
        $ret['select_lesson'] = 0;
        if(!empty($item->get_tag('lesson'))){
          $ret['select_lesson'] = $item->get_tag('lesson')->tag_value;
        }
        if(isset($item['teachers']) && count($item['teachers'])>0){
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
      if($this->is_student_or_parent($param['user']->role)==true){
        unset($param['fields']['status_name']);
      }
      else {
        $param['fields']['enable_date'] = [
          'label' => __('labels.setting_term'),
          'size' => 12,
        ];
        $param['fields']['remark'] = [
          'label' => __('labels.remark'),
          'size' => 12,
        ];
      }
      $form = $request->all();
      if(!isset($form['action'])){
        $form['action'] = '';
        if($param['user']->role=='manager' && $param['item']->status=='dummy'){
          //事務がダミーステータスの詳細を開いた場合
          $param['action'] = 'dummy_release';
        }
      }
      $page_title = $this->page_title($param['item'], "");
      if($request->has('user')){
        return view('calendars.simplepage', ["subpage"=>'', "page_title" => $page_title])->with($param);
      }
      return view($this->domain.'.page', $form)->with($param);
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
      if(!$this->is_student_or_parent($param['user']->role)){
        $param['fields']['enable_date'] = [
          'label' => __('labels.setting_term'),
          'size' => 12,
        ];
        $param['fields']['remark'] = [
          'label' => __('labels.remark'),
          'size' => 12,
        ];
      }
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
      if(!$this->is_student_or_parent($param['user']->role)){
        $param['fields']['enable_date'] = [
          'label' => __('labels.setting_term'),
          'size' => 12,
        ];
        $param['fields']['remark'] = [
          'label' => __('labels.remark'),
          'size' => 12,
        ];
      }
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
        $settings = $user->details()->get_calendar_settings([]);
        $param = $this->get_param($request);
      }
      else {
        $param = $this->get_param($request, $id);
        $settings = [$param['item']];
      }
      if(count($settings) < 1) {
        return $this->bad_request("setting not found");
      }
      if($param['user']->role !== 'manager'){
        //自分以外の設定があったらforbidden
        foreach($settings as $setting){
          if($setting->user_id != $param['user']->user_id){
            return $this->forbidden("This User is not manager role.");
          }
        }
      }
      if(!$request->has('start_date') || !$request->has('end_date')){
        return $this->bad_request();
      }
      $items = [];
      foreach($settings as $setting){
        //TODO:体験の場合、未来の開始日でも予定を登録することがある
        //if($setting->is_enable()==false) continue;
        if($setting->has_enable_member()==false) continue;
        $items[$setting->id] = $setting->get_add_calendar_date($request->start_date, $request->end_date, 1, 5);
      }

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
            $setting->memberAdd($student->user_id, $form['create_user_id']);
          }
          foreach($setting->members as $member){
            $is_delete = true;
            if($member->user_id == $setting->user_id) continue;
            foreach($form['students'] as $student){
              if($member->user_id == $student->user_id){
                $is_delete = false;
                break;
              }
            }
            if($is_delete == true){
              //既存メンバーが指定されていない場合、削除
              $member->dispose($param['user']->user_id);
            }
          }
        }
        $res = $setting->change($form);
        //TODO 更新失敗しても更新成功のエラーメッセージが表示されてしまう
        return $this->api_response(200, '', '', $setting);
      }, '更新', __FILE__, __FUNCTION__, __LINE__ );
    }



    /**
     * 新規登録
     *
     * @return \Illumin156ate\Http\Response
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
        $res = UserCalendarSetting::add($form);
        if($this->is_success_response($res)==true){
          $setting  = $res['data'];
          //生徒をカレンダーメンバーに追加
          if(!empty($form['students'])){
            foreach($form['students'] as $student){
               $setting->memberAdd($student->user_id, $form['create_user_id']);
            }
          }
          $setting = $res["data"]->details();
          $is_sendmail = false;
          if(isset($form['send_mail']) && $form['send_mail'] == "teacher"){
            $is_sendmail = true;
            //新規登録時に変更メールを送らない
            unset($form['send_mail']);
          }
          if($is_sendmail == true){
            $setting->register_mail([], $form['create_user_id']);
          }
          $this->send_slack('追加/ id['.$setting['id'].']生徒['.$setting['student_name'].']講師['.$setting['teacher_name'].']', 'info', '追加');
        }
        return $res;

      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );

      return $res;
    }
    public function _delete(Request $request, $id)
    {
      return $res = $this->transaction($request, function() use ($request, $id){
        $param = $this->get_param($request, $id);
        $setting = UserCalendarSetting::where('id', $id)->first();
        if($setting->is_group()==true){
          $form = $request->all();
          foreach($setting->members as $member){
            if(isset($form[$member->id.'_delete']) && $form[$member->id.'_delete']=='delete'){
              $member->dispose($param['user']->user_id);
            }
          }
        }
        else {
          $setting->dispose($param['user']->user_id);
        }
        return $this->api_response(200, '', '', $setting);
      }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
    }
    public function to_calendar(Request $request, $id=0)
    {
      set_time_limit(1200);
      $res = null;
      if(!$request->has('start_date') || !$request->has('end_date')){
        return $this->bad_request();
      }
      $param = $this->get_param($request, $id);
      if($param['user']->role !== 'manager'){
        $res = $this->forbidden();
      }
      if(!$request->has('select_dates')){
        $res = $this->bad_request();
      }
      $settings = [];
      if(!empty($id)){
        $settings[] = $param['item'];
      }
      else {
        if(!$request->has('user_id')){
          $res = $this->bad_request();
        }
        $user = User::where('id', $request->get('user_id'))->first();
        if(!isset($user)){
          return $this->bad_request("user not found");
        }
        $settings = $user->calendar_settings;
      }

      $res = $this->transaction($request, function() use ($request, $settings){
        foreach($settings as $setting){
          //TODO:体験の場合、未来の開始日でも予定を登録することがある
          //if($setting->is_enable()==false) continue;
          if($setting->has_enable_member()==false) continue;
          $dates = $setting->get_add_calendar_date($request->start_date, $request->end_date, 1, 5);
          foreach($dates as $date => $val){
            if(empty($date)) continue;
            $result = $setting->add_calendar(date('Y-m-d', strtotime($date)));
            if(!$this->is_success_response($result) && $result['message'] != 'already_registered'){
              //error
              $res = $this->error_response('繰り返しスケジュール登録エラー', $result["description"]);
              $this->send_slack('繰り返しスケジュール登録エラー/ id['.$setting['id'].']登録日付['.$date.']', 'error', '繰り返しスケジュール登録');
              return $res;
            }
          }
        }
        return $this->api_response(200, '', '', $settings);
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
      $res = $this->transaction($request, function() use ($request, $id, $setting, $param){
        $calendars = UserCalendar::where('user_calendar_setting_id', $id)
                    ->whereIn('id', $request->get('select_ids'))->get();
        foreach($calendars as $calendar){
          $calendar->dispose($param['user']->user_id, false);
        }
        return $this->api_response(200, '', '', $setting);
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
      $param['lesson_id'] = 0;
      $param['student_id'] = 0;

      if($request->has('lesson_id')){
        $param['lesson_id'] = $request->get('lesson_id');
      }

      if($request->has('trial_id')){
        //体験授業申し込みからの指定
        $trial_id = intval($request->get('trial_id'));
        $trial = Trial::where('id', $trial_id)->first();
        $param['trial_id'] = $trial_id;
        $param['item']->trial_id = $trial_id;
        $candidate_teachers = $trial->candidate_teachers(0,0);
        $lesson_id = 0;
        if($request->has('lesson_id')){
          $lesson_id = $request->get('lesson_id');
          $param['teachers'] = $candidate_teachers[$lesson_id];
        }
        else {
          $param['teachers'] = [];
          foreach($candidate_teachers as $lesson_id => $teachers){
            $param['teachers'] = array_merge($param['teachers'], $teachers);
          }
        }
        $student = $trial->student;
        $param['student_id'] = $student->id;
      }

      if($param['user']->role==="teacher"){
        $param['teachers'][] = $param['user'];
        $param['teacher_id'] = $param['user']->id;
      }
      else if($param['user']->role==="manager"){
        if($request->has('teacher_id')){
          $param['teachers'] = [];
          $param['teachers'][] = Teacher::where('id', $request->get('teacher_id'))->first();
          $param['teacher_id'] = $request->get('teacher_id');
        }
        else if($request->has('manager_id')){
          //事務からの登録の場合、作業内容＝9 (事務作業）
          $param['item']->work = 9;
        }

      }
      if($param['item']->work!=9 && !isset($param['teacher_id'])) {
        if(count($param["teachers"]) == 0) $param["teachers"] = Teacher::findStatuses(["regular"])->get();
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
    public function get_fee(Request $request, $id){
      if(!$request->has('student_id')){
        return $this->bad_request('student_id not found');
      }
      $item = UserCalendarSetting::where('id', $id)->first();
      if(!isset($item)){
        return $this->notfound();
      }

      $student = Student::where('id', $request->get('student_id'))->first();
      if(!isset($student)){
        return $this->bad_request('student no exist');
      }

      $target_member = null;
      foreach($item->members as $member){
        if($member->user_id == $student->user_id){
          $target_member = $member;
          break;
        }
      }
      if($target_member==null){
        return $this->bad_request('this setting does not has student.');
      }
      $res = $target_member->get_lesson_fee();
      if($res==null){
        return $this->error_response('api error');
      }
      return $res;
    }
    public function page_access_check(Request $request, $id){
      $this->user_key_check($request);
      $setting = UserCalendarSetting::where('id', $id)->first();
      if(!isset($setting)) abort(404, 'ページがみつかりません(102)');
      if($request->has('user') && $request->has('key')){
        $is_find = false;
        foreach($setting->members as $member){
          if($member->user_id == $request->get('user')){
            //指定したuserがcalendar.memberに存在する
            $is_find = true;
            break;
          }
        }
        if($is_find === false){
          abort(404, 'ページがみつかりません(11)');
        }
      }
      $this->user_login($request->get('user'));
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']);
      if($this->is_student_or_parent($param['user']->role)==true){
        unset($param['fields']['status_name']);
      }
      else {
        $param['fields']['enable_date'] = [
          'label' => __('labels.setting_term'),
          'size' => 12,
        ];
        $param['fields']['remark'] = [
          'label' => __('labels.remark'),
          'size' => 12,
        ];
      }
      $param['action'] = '';
      return $param;
    }
    /**
     * ステータス更新ページ
     *
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function status_update_page(Request $request, $id, $status)
    {
      if(!$request->has('user')){
        if (!View::exists($this->domain.'.'.$status)) {
          abort(404, 'ページがみつかりません(100)');
        }
      }
      $param = $this->page_access_check($request, $id);
      $page_title = $this->page_title($param['item'], $status);
      if($request->has('user')){
        if($status=='fix'){
          $member = UserCalendarMemberSetting::where('user_calendar_setting_id', $id)->where('user_id', $request->get('user'))->first();
          if($member->status==$status || $member->status=='cancel')   return redirect('/'.$this->domain.'/'.$param['item']->id.'?user='.$request->get('user'));
        }
        return view('calendars.simplepage', ["subpage"=>$status,"page_title" => $page_title ])->with($param);
      }

      return view($this->domain.'.'.$status, [])->with($param);
    }
    /**
     * ステータス更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Request  $request
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function status_update(Request $request, $id, $status)
    {
      $param = $this->get_param($request, $id);
      \Log::warning("UserCalendarSettingController::_status_update(".$status.")");
      $res = $this->transaction($request, function() use ($request, $param, $id, $status){
        $form = $request->all();
        $param['item'] = $this->model()->where('id', $param['item']->id)->first();
        $members = $param['item']->members;
        $_remark = '';
        $_access_key = '';
        if($status=='new' && $param['item']->status=='dummy'){
          $param['item']->update(['status' => 'new']);
          $param['item']->register_mail([], $param['user']->user_id);
          return $this->api_response(200, '', '', $param['item']);
        }

        if($status==='cancel'){
          $_remark = $request->get('cancel_reason');
        }
        else if($status==='rest'){
          $_remark = $request->get('rest_reason');
        }

        if($param['item']->work!=9){
          foreach($members as $member){
            //メンバーステータスの個別指定がある場合
            if(isset($form['is_all_student']) && $form['is_all_student']==1){
              //全生徒指定がある場合
              $member->status_update($status, $_remark, $param['user']->user_id);
            }
            else if(!empty($form[$member->id.'_status'])){
              $member->status_update($form[$member->id.'_status'], $_remark, $param['user']->user_id);
            }
          }
        }
        else {
          foreach($members as $member){
            $member->status_update($status, $_remark, $param['user']->user_id);
            break;
          }
        }
        return $this->api_response(200, '', '', $param['item']);
      }, 'カレンダーステータス更新', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, $this->status_update_message[$status]);
    }

}
