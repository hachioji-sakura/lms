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
        'lesson_week_name' => [
          'label' => '曜日',
          'size' => 6,
        ],
        'timezone' => [
          'label' => '時間帯',
          'size' => 6,
        ],
        'teacher_name' => [
          'label' => '講師',
          'size' => 6,
        ],
        'place_name' => [
          'label' => '場所',
          'size' => 6,
        ],
        'lesson' => [
          'label' => 'レッスン',
          'size' => 6,
        ],
        'course' => [
          'label' => 'コース',
          'size' => 6,
        ],
        'subject' => [
          'label' => '科目',
          'size' => 6,
        ],
        'student_name' => [
          'label' => '生徒',
          'size' => 6,
        ],
      ];
      return $ret;
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
      if($this->is_manager($user->role)===false){
        abort(403, 'このページにはアクセスできません(1)');
      }
      //$user = User::where('id', 607)->first()->details();
      $ret = [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        'user' => $user,
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
          $ret['candidate_teacher']["enable_subject"] = $item['teachers'][0]->user->teacher->get_enable_subjcet($ret['select_lesson']);
        }
      }
      return $ret;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $param = $this->get_param($request);
      $items = DB::table($this->table)->get();
      return $items->toArray();
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
      return view($this->domain.'.page', [
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
