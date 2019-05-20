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

    public function update_form(Request $request){
      $form = [];

      return $form;
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
        'user' => $user,
        'search_work' => $request->search_work,
        'search_week' => $request->search_week,
        'search_place' => $request->search_place,
        '_page' => $request->get('_page'),
        '_line' => $request->get('_line'),
        'attributes' => $this->attributes(),
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function teacher_index(Request $request, $teacher_id)
    {
      $teacher = Teacher::where('id', $teacher_id)->first();
      if(!isset($teacher)) abort(404);

      $param = $this->get_param($request);
      $request->merge([
        'teacher_id' => $teacher_id,
      ]);
      $request->merge([
        '_origin' => 'teachers/'.$teacher_id.'/calendar_settings',
      ]);
      return $this->index($request);
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
      }, '通常授業設定更新', __FILE__, __FUNCTION__, __LINE__ );
    }
    public function _delete(Request $request, $id)
    {
      return $res = $this->transaction(function() use ($request, $id){
        $setting = UserCalendarSetting::where('id', $id)->first();
        $setting->dispose();
        return $setting;
      }, '通常授業設定削除', __FILE__, __FUNCTION__, __LINE__ );
    }
}
