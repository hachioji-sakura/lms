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
use App\Models\Ask;
use DB;
use View;
class UserCalendarController extends MilestoneController
{
  public $domain = 'calendars';
  public $table = 'user_calendars';

  public $status_update_message = [
          'fix' => '授業予定を確認しました。',
          'confirm' => '授業予定の確認連絡をしました。',
          'cancel' => '授業予定をキャンセルしました。',
          'rest' => '休み連絡をしました。',
          'rest_cancel' => '休み取り消し依頼連絡をしました。',
          'lecture_cancel' => '休講依頼連絡をしました。',
          'presence' => '授業を出席に更新しました。',
          'absence' => '授業を欠席に更新しました。',
          'remind' => '授業予定の確認連絡をしました。',
        ];
  public function page_title($item, $page_status){
    if($item->is_teaching()==true){
      $title = $item->teaching_type_name();
    }
    else {
      $title = $item->work();
    }

    switch($page_status){
      case "rest":
        $title.="のお休み連絡";
        break;
      case "confirm":
        $title.="のご確認";
        break;
      default:
        $title.="詳細";
    }
    return $title;
  }
  public function model(){
    return UserCalendar::query();
  }
  public function show_fields($item){
    $base_ret = [
      'datetime' => [
        'label' => __('labels.datetime'),
      ],
      'status_name' => [
        'label' => __('labels.status'),
        'size' => 6,
      ],
      'place_floor_name' => [
        'label' => __('labels.place'),
        'size' => 6,
      ],
    ];
    if($item->work==9){
      //事務作業
      $ret = [
        'manager_name' => [
          'label' => __('labels.charge_user'),
          'size' => 6,
        ],
        'work_name' => [
          'label' => __('labels.schedule_details'),
          'size' => 6,
        ],
      ];
    }
    else if($item->is_management()==true){
      //授業予定以外
      $ret = [
        'teacher_name' => [
          'label' => __('labels.teachers'),
          'size' => 6,
        ],
        'work_name' => [
          'label' => __('labels.schedule_details'),
          'size' => 6,
        ],
        'student_name' => [
          'label' => __('labels.students'),
          'size' => 12,
        ],
      ];
    }
    else if($item->work==5 || $item->work==11){
      //授業予定
      $ret = [
        'teaching_name' => [
          'label' => __('labels.lesson_name'),
          'size' => 6,
        ],
        'student_name' => [
          'label' => __('labels.students'),
          'size' => 12,
        ],
      ];
    }
    else {
      //授業予定
      $ret = [
        'teacher_name' => [
          'label' => __('labels.teachers'),
          'size' => 6,
        ],
        'lesson' => [
          'label' => __('labels.lesson'),
          'size' => 6,
        ],
        'course' => [
          'label' => __('labels.lesson_type'),
          'size' => 6,
        ],
        'teaching_name' => [
          'label' => __('labels.lesson_name'),
          'size' => 6,
        ],
        'subject' => [
          'label' => __('labels.subject'),
          'size' => 6,
        ],
        'student_name' => [
          'label' => __('labels.students'),
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
  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    $form = $request->all();
    if(isset($form['course_type']) && $form['course_type'] == 'family' && isset($form['student_id'])){
      //生徒が全員家族かどうかチェック
      foreach($form['student_id'] as $student_id1){
        $student1 = Student::where('id', $student_id1)->first();
        foreach($form['student_id'] as $student_id2){
          if($student_id1==$student_id2) continue;
          if($student1->is_family($student_id2)==false){
            return $this->bad_request("ファミリーの場合、家族の生徒を登録してください。");
          }
        }
      }
    }
    return $this->api_response(200, '', '');
  }

  /**
   * 新規登録用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['create_user_id'] = $user->user_id;
    $schedule_type = "";
    if($request->has('schedule_type')){
      $schedule_type = $request->get('schedule_type');
    }
    //予定の指定
    if($request->has('start_date') && $request->has('start_hours') && $request->has('start_minutes')){
      $form['start_date'] = $request->get('start_date');
      $form['start_hours'] = $request->get('start_hours');
      $form['start_minutes'] = $request->get('start_minutes');
      $start_time = $form['start_date'].' '.$form['start_hours'].':'.$form['start_minutes'].':00';
      //授業時間＋開始日時から終了日時を計算
      $form['start_time'] = $start_time;
    }

    if($request->has('course_minutes')){
      //授業時間設定がある
      $form['course_minutes'] = $request->get('course_minutes');
      //授業予定の場合、授業時間から取得
      if($schedule_type=='class') $form['end_time']= date('Y/m/d H:i:s', strtotime($start_time.' +'.$form['course_minutes'].' minutes'));
    }

    if($request->has('end_hours') && $request->has('end_minutes')){
      $form['end_hours'] = $request->get('end_hours');
      $form['end_minutes'] = $request->get('end_minutes');
      //授業予定登録でなければ、終了時刻から取得
      if($schedule_type!='class')  $form['end_time'] = $form['start_date'].' '.$form['end_hours'].':'.$form['end_minutes'].':00';
    }

    $form['charge_subject'] = $request->get('charge_subject');
    $form['english_talk_lesson'] = $request->get('english_talk_lesson');
    $form['piano_lesson'] = $request->get('piano_lesson');
    $form['kids_lesson'] = $request->get('kids_lesson');
    $form['lesson'] = $request->get('lesson');
    $form['place_floor_id'] = $request->get('place_floor_id');
    $form['is_exchange'] = false;

    //講師の指定
    if($request->has('teacher_id')){
      $form['teacher_id'] = $request->get('teacher_id');
      $teacher = Teacher::where('id', $form['teacher_id'])->first();
      if(!isset($teacher)){
        //講師が存在しない
        abort(400, "存在しない講師");
      }
      $form['teacher_user_id'] = $teacher->user_id;
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

    $form['exchanged_calendar_id'] = 0;
    if($request->has('exchanged_calendar_id') && $request->get('exchanged_calendar_id') > 0){
      $form['exchanged_calendar_id'] = $request->get('exchanged_calendar_id');
    }

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

    if($request->has('rest_reason') && !empty($request->get('rest_reason'))) {
      $form['rest_reason'] = $request->get('rest_reason');
    }
    if($request->has('cancel_reason') && !empty($request->get('cancel_reason'))) {
      $form['cancel_reason'] = $request->get('cancel_reason');
    }
    if($request->has('remark') && !empty($request->get('remark'))) {
      $form['remark'] = $request->get('remark');
    }
    if($request->has('user') && !empty($request->get('user'))) {
      $form['user_id'] = $request->get('user');
    }
    if($request->has('send_mail')){
      $form['send_mail'] = $request->get('send_mail');
    }
    if($request->has('to_status')){
      $form['to_status'] = $request->get('to_status');
    }

    return $form;
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
    $ret = $this->get_common_param($request);
    $ret['remind'] = false;
    $ret['token'] = false;
    $ret['is_exchange_add'] = false;
    $ret['is_proxy'] = false;
    if($request->has('is_proxy')){
      $ret['is_proxy'] = true;
    }
    if($request->has('access_key')){
      $ret['token'] = $request->get('access_key');
    }
    if($request->has('rest_reason')){
      $ret['rest_reason'] = $request->get('rest_reason');
    }
    if($request->has('cancel_reason')){
      $ret['cancel_reason'] = $request->get('cancel_reason');
    }
    if($request->has('user_calendar_setting_id')){
      $ret['user_calendar_setting_id'] = $request->get('user_calendar_setting_id');
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

      if($this->is_manager_or_teacher($user->role)){
        //講師・事務の場合、すべての生徒名を表示する(details(user_id=1)）
        $ret['item'] = $item->details(1);
      }
      else {
        //それ以外は、自分に関連するもの（親子）のみ表示する
        $ret['item'] = $item->details($user->user_id);
      }
      if($request->has('student_id')){
        $student = Student::where('id', $request->get('student_id'))->first();
        if(isset($student)){
          $ret['item']->own_member = $ret['item']->get_member($student->user_id);
          $ret['item']["status"] = $ret['item']->own_member->status;
          $ret['item']["status_name"] = $ret['item']->own_member->status_name();
          $ret['item']["student_name"] = $student->name();
        }
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
      /*
      if(!$request->has('_sort')){
        $request->merge([
          '_sort' => 'start_time',
          '_sort_order' => 'desc',
        ]);
      }
      */
      $param = $this->get_param($request);
      $_table = $this->search($request);
      $_table["count"] = 0;
      $page_data = $this->get_pagedata($_table["count"] , $param['_line'], $param["_page"]);
      foreach($page_data as $key => $val){
        $param[$key] = $val;
      }
      return view($this->domain.'.lists', $_table)
        ->with($param);
    }
    public function space_calendars(Request $request)
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
      /*
      if(!$request->has('_sort')){
        $request->merge([
          '_sort' => 'start_time',
          '_sort_order' => 'desc',
        ]);
      }
      */
      $param = $this->get_param($request);
      $_table = $this->search($request);
      $_table["count"] = 0;
      $page_data = $this->get_pagedata($_table["count"] , $param['_line'], $param["_page"]);
      foreach($page_data as $key => $val){
        $param[$key] = $val;
      }
      return view($this->domain.'.space_lists', $_table)
        ->with($param);
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
        '_domain' => 'teachers/'.$teacher_id.'/'.$this->domain,
      ]);
      return $this->index($request);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function api_index(Request $request, $user_id=0, $from_date=null, $to_date=null)
    {
      set_time_limit(600);
      $param = $this->get_param($request);
      if(!empty($from_date) && strlen($from_date)===8){
        $from_date = date('Y-m-d', strtotime($from_date));
        $request->merge([
          'from_date' => $from_date,
        ]);
      }
      if(!empty($to_date) && strlen($to_date)===8){
        $to_date = date('Y-m-d', strtotime($to_date));
        $request->merge([
          'to_date' => $to_date,
        ]);
      }
      $user = $this->login_details($request);
      if(!isset($user)){
        return $this->forbidden();
      }
      $items = $this->model();
      if($this->is_student_or_parent($user->role)){
        $items = $items->where('status', '!=', 'new');
      }
      if($request->has('student_id')){
        $s = Student::where('id', $request->get('student_id'))->first();
        $user_id = $s->user_id;
      }
      else if($request->has('teacher_id')){
        $t = Teacher::where('id', $request->get('teacher_id'))->first();
        $user_id = $t->user_id;
      }
      if($user_id==0 && $this->is_manager_or_teacher($user->role)!=true){
         return $this->forbidden("This User is not manager or tehacer role.");
      }

      if($request->has('is_all_data') && ($request->get('is_all_data')==1 || $request->get('is_all_data')[0]==1)){
        if($this->is_student_or_parent($param['user']->role)==false){
          $user_id = 0;
        }
        else {
          //講師・事務のみカレンダーの閲覧可能
          return $this->bad_request();
        }
      }

      if($user_id > 0){
        if($this->is_student($user->role) && $user->user_id != $user_id) return $this->forbidden("is not owner");
        if($this->is_parent($user->role)){
          $s = Student::where('user_id', $user_id)->first();
          if(!isset($s) || $s->is_parent($user->id)!=true) return $this->forbidden("is not family");
        }
        $items = $items->findUser($user_id);
      }

      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);
      $items = $this->_search_sort($request, $items);
      \Log::warning("--------------UserCalendarController::api_index  start---------------------------");
      \Log::warning($items->toSql());
      $items = $items->get();
      \Log::warning("--------------UserCalendarController::api_index  end---------------------------");
      foreach($items as $item){
        $item = $item->details($user_id);
        if($user_id > 0) {
          $item->own_member = $item->get_member($user_id);
          if(isset($item->own_member)){
            $item->status = $item->own_member->status;
          }
        }
      }

      return $this->api_response(200, "", "", $items->toArray());
    }
    public function api_english_group(Request $request){
      if(!$request->has('from_date')){
        $request->merge([
          'from_date' => date('Y-m-d', strtotime("+1 day")),
        ]);
      }
      if(!$request->has('to_date')){
        $request->merge([
          'to_date' => date('Y-m-t', strtotime("+1 month")),
        ]);
      }

      $param = $this->get_param($request);

      $items = $this->model();
      $items = $items->where('status', 'fix');
      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);
      $items = $this->_search_sort($request, $items);
      $items = $items->orderBy('start_time')->get();

      $ret = [];
      foreach($items as $item){
        if($item->is_group()==false) continue;
        if(!$item->is_english_talk_lesson()) continue;
        if($request->has('english_teacher')){
          if($item->user->has_tag('english_teacher', $request->get('english_teacher'))==false){
            continue;
          }
        }
        if($request->has('english_talk_lesson')){
          if($item->user->has_tag('english_talk_lesson', $request->get('english_talk_lesson'))==false){
            continue;
          }
        }
        $item = $item->details(0);
        $ret[] = $item;
      }
      return "";
      return $this->api_response(200, "", "", $ret);
    }
    public function search(Request $request)
    {
      $user = $this->login_details($request);
      if(!isset($user)) return $this->forbidden();
      if($this->is_manager($user->role)!=true) return $this->forbidden();
      $items = $this->model();
      $items = $this->_search_scope($request, $items);
      $count = $items->count();
      $items = $this->_search_pagenation($request, $items);
      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $item = $item->details(1);
      }
      $fields = [
        "id" => [
          "label" => "ID",
        ],
        "datetime" => [
          "label" => __('labels.datetime'),
          "link" => "show",
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
            "edit",
            "delete"]
        ]
      ];
      return ["items" => $items->toArray(), "fields" => $fields, "count" => $count];
    }

    /**
     * フィルタリングロジック
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Collection $items
     * @return Collection
     */
    public function _search_scope(Request $request, $items)
    {
      $form = $request->all();

      //ID 検索
      if(isset($form['id'])){
        $items = $items->where('id',$form['id']);
      }
      //設定ID　検索
      if(isset($form['user_calendar_setting_id'])){
        $items = $items->where('user_calendar_setting_id',$form['user_calendar_setting_id']);
      }
      //ステータス 検索
      if(isset($form['search_status'])){
        if(gettype($form['search_status']) == "array") $items = $items->findStatuses($form['search_status']);
        else $items = $items->findStatuses(explode(',', $form['search_status'].','));
      }
      //ワーク 検索
      if(isset($form['search_work'])){
        $_param = "";
        if(gettype($form['search_work']) == "array") $_param  = $form['search_work'];
        else $_param = explode(',', $form['search_work'].',');
        $items = $items->findWorks($_param);
      }
      //授業タイプ 検索
      if(isset($form['teaching_type'])){
        $_param = "";
        if(gettype($form['teaching_type']) == "array") $_param  = $form['teaching_type'];
        else $_param = explode(',', $form['teaching_type'].',');
        $items = $items->findTeachingType($_param);
      }
      //場所 検索
      if(isset($form['search_place'])){
        $_param = "";
        if(gettype($form['search_place']) == "array") $_param  = $form['search_place'];
        else $_param = explode(',', $form['search_place'].',');
        $items = $items->findPlaces($_param);
      }
      //講師ID
      if(isset($form['teacher_id'])){
        $teacher = Teacher::where('id',$form['teacher_id'])->first();
        if(isset($teacher)) $items = $items->findUser($teacher->user_id);
      }
      //生徒ID
      if(isset($form['student_id'])){
        $student = Student::where('id',$form['student_id'])->first();
        if(isset($student)) {
          //振替元対象
          if(isset($form['exchange_target']) && isset($form['lesson'])){
            $items = $items->findExchangeTarget($student->user_id, $form['lesson']);
          }
          else {
            $items = $items->findUser($student->user_id);
          }
        }
      }
      else {

      }
      if(isset($form['exchange_lesson']) && $form['exchange_lesson']==1){
        $items = $items->where('exchanged_calendar_id','>', 0);
      }
      if(isset($form['trial_lesson']) && $form['trial_lesson']==1){
        $items = $items->where('trial_id','>', 0);
      }

      //更新取得
      if(isset($form['update'])){
        $items = $items->where('updated_at','>',$form['update']);
      }

      //日付検索
      $from_date = "";
      $to_date = "";
      if(isset($request->from_date)){
        $from_date = $request->from_date;
        if(mb_strlen($from_date) < 11) $from_date .=' 00:00:00';
      }
      if(isset($request->to_date)){
        $to_date = $request->to_date;
        if(mb_strlen($to_date) < 11) $to_date .=' 23:59:59';
      }
      if(!empty($from_date) || !empty($to_date)){
        $items = $items->rangeDate($from_date, $to_date);
      }
      //検索ワード
      if(isset($request->search_keyword)){
        $items = $items->searchWord($request->search_keyword);
      }

      return $items;
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

      $form = $request->all();
      if(!isset($form['action'])){
        $form['action'] = '';
      }
      $page_title = $this->page_title($param['item'], "");
      if($request->has('user')){
        return view('calendars.simplepage', ["subpage"=>'', "page_title" => $page_title])->with($param);
      }
      return view($this->domain.'.page', $form)->with($param);
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
      $param['ask'] = $this->get_ask_data($request, $param, $status);

      $page_title = $this->page_title($param['item'], $status);
      if($request->has('user')){
        if($status=='fix' && $param['item']->status=='fix'){
          return redirect('/'.$this->domain.'/'.$param['item']->id.'?user='.$request->get('user'));
        }
        return view('calendars.simplepage', ["subpage"=>$status,"page_title" => $page_title ])->with($param);
      }

      return view($this->domain.'.'.$status, [])->with($param);
    }
    private function get_ask_data(Request $request, $param, $status){
      $ask = null;
      if($status!="rest_cancel" && $status!="lecture_cancel") return null;
      //休み取り消し依頼 or 休講申請
      $ask_form = [
        'type'=>$status,
        'status'=>'new'
      ];
      if($status=="lecture_cancel"){
        //休講申請の場合の担当はuser_id=1(事務)
        $ask_form['charge_user_id'] = 1;
      }
      $member = $this->get_member_data($request, $param);
      if($member!=null){
        $ask = $member->already_ask_data($ask_form);
      }
      return $ask;
    }
    private function get_member_data(Request $request, $param){
      $member = null;
      if($request->has('student_id')){
        $student = Student::where('id', $request->student_id)->first();
        $member = $param['item']->members->where('user_id', $student->user_id)->first();
      }
      else {
        $member = $param['item']->members->where('user_id', $param['item']->user_id)->first();
      }
      return $member;
    }
    /**
     * ステータス更新ページ
     *
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function rest_change_page(Request $request, $id)
    {
      $param = $this->page_access_check($request, $id);
      unset($param['fields']['remark']);
      unset($param['fields']['status_name']);
      unset($param['fields']['lesson']);
      unset($param['fields']['course']);
      unset($param['fields']['subject']);
      unset($param['fields']['teaching_name']);
      unset($param['fields']['place_floor_name']);
      return view($this->domain.'.rest_change', [])->with($param);
    }
    public function teacher_change_page(Request $request, $ask_id)
    {
      $ask = Ask::where('id', $ask_id)->first();
      if(!isset($ask)){
        abort(404);
      }
      $id = 0;
      if($ask->target_model_id > 0 && $ask->target_model=='user_calendar_members'){
        $m = UserCalendarMember::where('id', $ask->target_model_id)->first();
        if(!isset($m)) abort(404);
        $id = $m->calendar->id;
      }
      if($id < 1) abort(404);
      $param = $this->get_param($request, $id);
      if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');

      $_teachers = Teacher::findStatuses(["regular"])->get();
      $teachers = [];
      $lesson = $param['item']->get_tag('lesson')->tag_value;
      foreach($_teachers as $teacher){
        if($teacher->user_id == $param['item']->user_id) continue;
        if(!$teacher->user->has_tag('lesson', $lesson)) continue;
        $teachers[] = $teacher;
      }
      $param['fields'] = $this->show_fields($param['item']);
      $param['action'] = '';
      $param['_edit'] = false;
      $param['teachers'] = $teachers;
      $param['ask'] = $ask;
      return view($this->domain.'.teacher_change', [])->with($param);
    }
    public function page_access_check(Request $request, $id){
      $this->user_key_check($request);
      $calendar = UserCalendar::where('id', $id)->first();
      if(!isset($calendar)) abort(404, 'ページがみつかりません(102)');
      if($request->has('user') && $request->has('key')){
        $is_find = false;
        foreach($calendar->get_access_member($request->get('user')) as $member){
          if($member->user_id == $request->get('user')){
            //指定したuserがcalendar.memberに存在する
            $is_find = true;
            break;
          }
        }
        if($is_find === false){
          abort(404, 'ページがみつかりません(99)');
        }
      }
      $this->user_login($request->get('user'));
      $param = $this->get_param($request, $id);
      $param['fields'] = $this->show_fields($param['item']);
      $param['action'] = '';
      return $param;
    }
    public function user_key_check(Request $request){
      if($request->has('user') && !$request->has('key')){
          abort(404, 'ページがみつかりません(1)');
      }
      if($request->has('user') && $request->has('key')){
        if(!$this->is_enable_token($request->get('key'))){
          abort(403, '有効期限が切れています(2)');
        }
      }
    }
    /**
     * カレンダー通知
     *
     * @param  Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function remind(Request $request, $id){
      $param = $this->get_param($request, $id);
      $res = $this->transaction($request, function() use ($param){
        if($param['item']->status=='new'){
          $param['item']->register_mail($param, $param['user']->user_id);
        }
        else {
          foreach($param['item']->members as $member){
            $u = $member->user->details();
            $member->remind($param['user']->user_id);
          }
        }
        return $this->api_response(200, '', '', $param['item']);
      }, 'カレンダー通知', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, $this->status_update_message["remind"]);
    }
    /**
     * 強制キャンセル更新
     *
     * @param  Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function force_cancel(Request $request, $id){
      $param = $this->get_param($request, $id);
      $res = $this->transaction($request, function() use ($request, $param, $id){
        if($param['item']->status=='new' || $param['item']->status=='confirm'){
          $remark = $param['item']->remark;
          if(!empty($request->get('cancel_reason'))){
            $remark.="\n".$request->get('cancel_reason');
          }
          UserCalendar::where('id', $id)->update(['status' => 'cancel']);
          UserCalendarMember::where('calendar_id', $id)->update(['status' => 'cancel']);
        }
        $title = __('messages.mail_title_calendar_cancel');
        $template = 'calendar_cancel';
        $param['item']->teacher_mail($title, $param, 'text', $template);
        return $this->api_response(200, '', '', $param['item']);
      }, 'カレンダー通知', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, $this->status_update_message["cancel"]);
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
      $res = $this->api_response();
      $is_send = true;
      $calendar = UserCalendar::where('id', $id)->first();
      if($status=='rest_cancel' || $status=="lecture_cancel"){
        $ask = $this->get_ask_data($request, $param, $status);
        if($ask==null){
          $member = $this->get_member_data($request, $param);
          switch($status){
            case "rest_cancel":
              $member->rest_cancel_ask($param['user']->user_id);
              break;
            case "lecture_cancel":
              $member->lecture_cancel_ask($param['user']->user_id);
              break;
          }
        }
        else {
          $ask->remind_mail($param['user']->user_id);
        }
      }
      else {
        $res = $this->_status_update($request, $param, $id, $status);
        $param['item'] = UserCalendar::where('id', $param['item']->id)->first();
      }

      return $this->save_redirect($res, $param, $this->status_update_message[$status]);
    }
    public function rest_change(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $res = $this->api_response();
      $is_send = true;

      $res = $this->_rest_change($request, $param, $id);

      $param['item'] = UserCalendar::where('id', $param['item']->id)->first();

      return $this->save_redirect($res, $param, '休み判定結果を変更しました');
    }
    public function _rest_change(Request $request, $param, $id){
      $form = $request->all();
      if($form['rest_type']!='a2' && $form['rest_type']!='a1') abort(400);
      if(empty($form['student_id']) || !is_numeric($form['student_id']) || $form['student_id']<1) abort(400);

      $calendar = UserCalendar::where('id', $id)->first();
      $student = Student::where('id', $form['student_id'])->first();
      if(!isset($student)) abort(400);
      $update_member = null;
      foreach($calendar->members as $member){
        if($member->user_id == $student->user_id){
          $update_member = $member;
        }
      }
      if($update_member==null) abort(400);

      $res = $this->transaction($request, function() use ($update_member, $form){
        $res = $update_member->update_rest_type($form['rest_type'], $form['rest_result']);
        return $this->api_response(200, '', '', $update_member);
      }, '休み種類変更', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    /**
     * カレンダーステータス更新
     *
     * @param  array  $param
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    private function _status_update(Request $request, $param, $id, $status){
      \Log::warning("UserCalendarController::_status_update(".$status.")");
      $res = $this->transaction($request, function() use ($request, $param, $id, $status){
        $form = $request->all();
        $param['item'] = $this->model()->where('id', $param['item']->id)->first();
        $members = $param['item']->members;
        $_remark = '';
        $_access_key = '';
        if($status==='cancel'){
          $_remark = $request->get('cancel_reason');
        }
        else if($status==='rest'){
          $_remark = $request->get('rest_reason');
        }

        //操作者のステータス更新
        $member_user_id = $param['user']->user_id;
        if($this->is_manager($param['user'])){
          //事務による代理登録=カレンダー主催者（講師）のステータスを更新
          $member_user_id = $param['item']->user_id;
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
            else if($member->user_id == $member_user_id && $param['is_proxy']==false){
              $member->status_update($status, $_remark, $member_user_id);
            }
          }
        }
        else {
          foreach($members as $member){
            $member->status_update($status, $_remark, $member_user_id);
            break;
          }
        }
        return $this->api_response(200, '', '', $param['item']);
      }, 'カレンダーステータス更新', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['teachers'] = [];
      $teacher = Teacher::where('user_id', $param['item']["user_id"])->first();
      if(isset($teacher)) $param['teachers'][] = $teacher;
      return view($this->domain.'.create', [
        '_edit' => true])
        ->with($param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $res = $this->_update($request, $id);
      $status = "";
      if($request->has('status')){
        $status = $request->get('status');
        return $this->status_update($request, $id, $request->get('status'));
      }
      if($request->has('user')){
        $param['result'] = $this->status_update_message[$status];
        $param['fields'] = $this->show_fields($param['item']);
        return $this->save_redirect($res, $param, '更新しました。', '/calendars/'.$param['item']['id'].'?user='.$param['user']->user_id.'&key='.$param['token']);
      }
      else {
        return $this->save_redirect($res, $param, '更新しました。');
      }
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $res = $this->transaction($request, function() use ($request,$id){
        $item = $this->model()->where('id',$id)->first();
        $form = $this->create_form($request);
        //statusは更新対象にしない
        if(!empty($form['status'])) unset($form['status']);
        $item->change($form);
        return $this->api_response(200, '', '', $item);
      }, '授業予定更新', __FILE__, __FUNCTION__, __LINE__ );

      return $res;
    }
    /**
     * 予定確認メール送信
     * カレンダーの対象者に確認メールを送信する
     * send_type: 生徒あて=student | 講師あて=teacher
     * template : emails\[template)_text.blade.phpを指定する
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $title
     * @param  string  $template
     * @return view
     */
    private function calendar_mail($param, $title, $template, $is_teacher_only=false){
      $item = $param['item']->details();
      $login_user = $param['user'];
      $send_from = "student";

      $is_proxy = $param['is_proxy'];
      if($is_proxy===true){
        $login_user = Student::where('id', $param['student_id'])->first()->user->details();
      }
      else {
        //代理ではない
        if($this->is_teacher($login_user->role)){
          $send_from = "teacher";
        }
        else if($this->is_manager($login_user->role)){
          $send_from = "manager";
        }
      }
      $members = $item->members;
      if($param['remind']===true){
        //$title .= '【再送】';
      }

      //送信対象を判定
      $send_dataset = [];
      foreach($members as $member){
        $email = $member->user['email'];
        $user = $member->user->details();
        $user_id = $user->user_id;
        $send_to = $user->role;
        $user_name = $user['name'];
        $is_child_member = false;
        $is_own = false;
        $student_id = 0;
        if($user->user_id===$login_user->user_id){
          //メンバー＝操作者
          $is_own = true;
        }
        if($is_teacher_only===true && $this->is_teacher($user->role)!==true){
          //講師のみのメールの場合、講師以外は無効
          continue;
        }
        if($this->is_student($user->role)){
          //対象が生徒の場合、保護者のメールアドレスを取得
          $student_id = $member->user->student->id;
          $relations = $member->user->student->relations;
          $email = '';
          foreach($relations as $relation){
            if($relation->parent->user_id === $login_user->user_id){
              //メンバー＝操作者の子供
              $is_child_member = true;
            }
            //TODO 先にとれたユーザーを操作する親にする（修正したい）
            $user_id = $relation->parent->user->id;
            $email = $relation->parent->user->email;
            //TODO 安全策をとるテスト用メールにする
            //$email = 'yasui.hideo+u'.$user_id.'@gmail.com';
          }
        }
        \Log::info("[".$user_name."][".$send_from."][".$send_to."][".$is_own."][".$is_child_member."][".$student_id."]");
        if($send_from==="student" && $send_to==='student'){
          //操作者が保護者または生徒で、送り先が生徒あての場合
          if($is_own===false && $is_child_member===false){
            //別の生徒にはメールを出さない(自身か、子であるべき）
            \Log::warning("[メール送信しない]");
            continue;
          }
        }
        $is_exist = 0;
        foreach($send_dataset as $i => $send_data){
          if($email ===$send_data['email']){
            //アドレス追加済み
            $is_exist = $i;
            //$send_dataset[$i]['student_ids'][] = $student_id;
            break;
          }
        }
        $user = User::where('id', $user_id)->first();
        if(isset($user)) $user = $user->details();
          if($is_exist==0){
            $_item = UserCalendar::where('id', $item->id)->first()->details($user_id);
            if(!empty($param['cancel_reason'])){
              $_item['cancel_reason'] = $param['cancel_reason'];
            }
            if(!empty($param['rest_reason'])){
              $_item['rest_reason'] = $param['rest_reason'];
            }
            $send_dataset[] = [
             'email' => $email,
             'send_to' => $send_to,
             'item' => $_item,
             'token' => $member->access_key,
             'user_name' => $user->name(),
             'user' => $user
            ];
         }
         else {
          $send_dataset[$is_exist]["user_name"] .= ','.$user->name();
        }
      }
      //送信対象にメール送信
      foreach($send_dataset as $send_data){
        $this->send_mail($send_data['email'],
         $title,
         [
         'login_user' => $login_user,
         'user' => $send_data['user'],
         'user_name' => $send_data['user_name'],
         'send_to' => $send_data['send_to'],
         'item' =>$send_data['item'],
         'token' => $send_data['token'],
         'user_id' => $user_id,
         'is_proxy' => $is_proxy
         ],
         'text',
         $template,
         $send_data['user']->user->get_locale()
       );
      }

      return true;
    }
    /**
     * 新規登録画面
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Request $request)
   {
      $param = $this->get_param($request);
      if($request->has('exchanged_calendar_id')){
        //振替元指定あり
        $param['is_exchange_add'] = true;

        $exchanged_calendar_id = intval($request->get('exchanged_calendar_id'));
        $exchanged_calendar = UserCalendar::where('id', $exchanged_calendar_id)->first();
        if(!isset($exchanged_calendar)) abort(404);
        $param['item'] = $exchanged_calendar->details(1);
        $param['item']['exchanged_calendar_id'] = $exchanged_calendar_id;
        $teacher = Teacher::where('user_id', $exchanged_calendar->user_id)->first();
        $param['teachers'][] = $teacher;
        $param['teacher_id'] = $teacher->id;
        $students = $exchanged_calendar->get_students();
        $student = Student::where('user_id', $students[0]->user_id)->first();
        $param['student_id'] = $student->id;
      }
      else {
        //新規
        $param['item'] = new UserCalendar();
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
      }
      $start_date = date('Y/m/d');
      if($request->has('start_date')){
        $start_date = date('Y/m/d', strtotime($request->get('start_date')));
      }
      $param['item']['start_date'] = $start_date;
      $param['item']['start_hours'] = intval($request->get('start_hours'));
      $param['item']['start_minutes'] = intval($request->get('start_minutes'));
      $param['item']['course_minutes'] = 0;
      if($request->has('course_minutes')){
        $param['item']['course_minutes'] = $request->get('course_minutes');
      }
      if($request->has('end_date') && $request->has('end_hours') && $request->has('end_minutes')){
        $param['item']['end_hours'] = intval($request->get('end_hours'));
        $param['item']['end_minutes'] = intval($request->get('end_minutes'));
      }
      return view($this->domain.'.create',
        [ 'error_message' => '', '_edit' => false])
        ->with($param);
    }
    public function teacher_create(Request $request, $teacher_id)
    {
      $request->merge([
        'item_id' => $teacher_id,
        'origin' => 'teachers'
      ]);
      return $this->create($request);
    }
    public function teacher_show(Request $request, $teacher_id, $id)
    {
      $request->merge([
        'item_id' => $teacher_id,
        'origin' => 'teachers'
      ]);
      return $this->show($request, $id);
    }
    public function teacher_edit(Request $request, $teacher_id, $id)
    {
      $request->merge([
        'item_id' => $teacher_id,
        'origin' => 'teachers'
      ]);
      return $this->edit($request, $id);
    }


    /**
     * 新規登録
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $param = $this->get_param($request);
      $res = $this->save_validate($request);
      if($this->is_success_response($res)){
        $res = $this->_store($request);
      }
      return $this->save_redirect($res, $param, 'カレンダーに予定を登録しました。');
    }

    /**
     * 新規登録ロジック
     *
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
      $holiday = (new UserCalendar())->get_holiday($request->start_time);
      if($holiday!=null && $holiday->is_private_holiday() == true){
        return $this->error_response('休校日のため予定は登録できません');
      }
      $res = $this->transaction($request, function() use ($request){
        $form = $this->create_form($request);
        if(empty($form['start_time']) || empty($form['end_time'])) {
          abort(400, "日時パラメータ不正");
        }

        $res = UserCalendar::add($form);
        if(!$this->is_success_response($res)){
          return $res;
        }
        $calendar = $res['data'];
        //生徒をカレンダーメンバーに追加
        if(!empty($form['students'])){
          foreach($form['students'] as $student){
            $calendar->memberAdd($student->user_id, $form['create_user_id']);
          }
        }
        $calendar = $calendar->details();

        $is_sendmail = false;
        if(isset($form['send_mail']) && $form['send_mail'] == "teacher"){
          $is_sendmail = true;
          //新規登録時に変更メールを送らない
          unset($form['send_mail']);
        }
        if($is_sendmail == true){
          $calendar->register_mail([], $form['create_user_id']);
        }

        $this->send_slack('カレンダー追加/ id['.$calendar['id'].'] status['.$calendar['status'].'] 開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', 'カレンダー追加');
        return $this->api_response(200, '', '', $calendar);
      }, '授業予定作成', __FILE__, __FUNCTION__, __LINE__ );

      if($res["data"]==null){
        return $this->error_response('競合する予定がすでに登録されています');
      }
      return $res;
    }
    /**
     * 授業予定削除処理
     *
     * @return \Illuminate\Http\Response
     */
    public function _delete(Request $request, $id)
    {
      $res = $this->transaction($request, function() use ($request, $id){
        $param = $this->get_param($request, $id);
        $user = $this->login_details($request);
        $calendar = $param["item"];
        if($calendar->is_group()==false){
          $calendar->dispose($user->user_id);
          $this->send_slack('カレンダー削除/ id['.$calendar['id'].'] status['.$calendar['status'].'] 開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', 'カレンダー削除');
        }
        else {
          $form = $request->all();
          foreach($calendar->members as $member){
            if(isset($form[$member->id.'_delete']) && $form[$member->id.'_delete']=='delete'){
              $member->dispose($param['user']->user_id);
            }
          }
        }
        return $this->api_response(200, '', '', $calendar);
      }, 'カレンダー削除', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    /**
     * 登録内容が有効かどうかチェック
     *
     * @return \Illuminate\Http\Response
     */
    public function setting_check(Request $request){
      //TODO : 設定を問題なく登録できるかチェックするAPI
      $ret = $this->api_response();
      return $ret;
    }
}
