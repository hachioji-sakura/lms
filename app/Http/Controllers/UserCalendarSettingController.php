<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Lecture;
use App\Models\ChargeStudent;
use App\Models\UserTag;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use DB;
use View;

class UserCalendarSettingController extends UserCalendarController
{
  public $domain = 'calendar_settings';
  public $table = 'user_calendar_settings';
  public $domain_name = 'カレンダー設定';
    public function model(){
      return UserCalendarSetting::query();
    }
    public function show_fields(){
      $user = $this->login_details();
        $ret = [
        'title1' => [
          'label' => '概要',
          'size' => 6,
        ],
        'place_name' => [
          'label' => '講師',
          'size' => 6,
        ],
        'title2' => [
          'label' => '詳細',
        ],
        'user_name' => [
          'label' => '担当',
          'size' => 6,
        ],
        'student_name' => [
          'label' => '生徒',
          'size' => 6,
        ],
        'subject' => [
          'label' => '科目',
          'size' => 12,
        ],
      ];
      return $ret;
    }
    public function create_form(Request $request){
      $user = $this->login_details();
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

    public function search(Request $request, $user_id=0)
    {
      $items = $this->model();

      //設定有効なものだけ表示（設定開始～終了）
      //$items = $items->enable();

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
          "label" => "曜日",
          "link" => "show",
        ],
        "timezone" => [
          "label" => "時間帯",
        ],
        "place_name" => [
          "label" => "場所",
        ],
        "work_name" => [
          "label" => "作業",
        ],
        "user_name" => [
          "label" => "担当",
        ],
        "student_name" => [
          "label" => "生徒",
        ],
        "subject" => [
          "label" => "科目",
        ],
        "buttons" => [
          "label" => "操作",
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
      $user = $this->login_details();
      if(!isset($user) || $this->is_manager($user->role)===false){
        abort(403, 'このページにはアクセスできません(1)');
      }
      //$user = User::where('id', 607)->first()->details();
      $ret = [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        'teacher_id' => $request->teacher_id,
        'user' => $user,
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
      $param['fields'] = $this->show_fields();
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
      $param['fields'] = $this->show_fields();
      $param['add_dates'] = $param['item']->get_add_calendar_date();

      return view($this->domain.'.to_calendar', [
        'action' => $request->get('action')
      ])->with($param);
    }
    public function _update(Request $request, $id)
    {
      return $res = $this->transaction(function() use ($request, $id){
        $form = $request->all();
        $param = $this->get_param($request, $id);
        $form['create_user_id'] = $param['user']->user_id;
        $setting = UserCalendarSetting::where('id', $id)->first();
        $setting->change($form);
        return $setting;
      }, $this->domain_name.'更新', __FILE__, __FUNCTION__, __LINE__ );
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

      return $this->save_redirect($res, $param, $this->domain_name.'を登録しました');
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
        $this->send_slack($this->domain_name.'追加/ id['.$setting['id'].']生徒['.$setting['student_name'].']講師['.$setting['teacher_name'].']', 'info', $this->domain_name.'追加');
        $param = $this->get_param($request, $setting->id);
        return $setting;
      }, $this->domain_name.'作成', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    public function _delete(Request $request, $id)
    {
      return $res = $this->transaction(function() use ($request, $id){
        $setting = UserCalendarSetting::where('id', $id)->first();
        $setting->dispose();
        return $setting;
      }, $this->domain_name.'削除', __FILE__, __FUNCTION__, __LINE__ );
    }
}
