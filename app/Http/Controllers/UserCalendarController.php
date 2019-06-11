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
  public $domain_name = '授業予定';
  public $status_update_message = [
          'fix' => '授業予定を確認しました。',
          'confirm' => '授業予定の確認連絡をしました。',
          'cancel' => '授業予定をキャンセルしました。',
          'rest' => '休み連絡をしました。',
          'rest_cancel' => '休み取り消し連絡をしました。',
          'presence' => '授業を出席に更新しました。',
          'absence' => '授業を欠席に更新しました。',
          'remind' => '授業予定の確認連絡をしました。',
        ];
  public function model(){
    return UserCalendar::query();
  }
  public function show_fields($work){
    if($work==9){
      $ret = [
        'datetime' => [
          'label' => '日時',
        ],
        'status_name' => [
          'label' => 'ステータス',
          'size' => 6,
        ],
        'manager_name' => [
          'label' => '担当',
          'size' => 6,
        ],
        'place_name' => [
          'label' => '場所',
          'size' => 6,
        ],
      ];
    }
    else {
      $ret = [
        'datetime' => [
          'label' => '日時',
        ],
        'status_name' => [
          'label' => 'ステータス',
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
        'teaching_name' => [
          'label' => '授業種類',
          'size' => 6,
        ],
        'subject' => [
          'label' => '科目',
          'size' => 6,
        ],
        'student_name' => [
          'label' => '生徒',
          'size' => 12,
        ],
      ];
    }
    return $ret;
  }

  /**
   * 更新用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function update_form(Request $request, $id=0){
    $param = $this->get_param($request, $id);
    $form = $request->all();
    $user = $param['user'];
    $form['create_user_id'] = $user->user_id;
    //予定の指定
    if($request->has('start_date') && $request->has('start_hours')
        && $request->has('start_minutes')){
      $form['start_date'] = $request->get('start_date');
      $form['start_hours'] = $request->get('start_hours');
      $form['start_minutes'] = $request->get('start_minutes');
      $form['end_hours'] = $request->get('end_hours');
      $form['end_minutes'] = $request->get('end_minutes');

      $start_time = $form['start_date'].' '.$form['start_hours'].':'.$form['start_minutes'].':00';
      $end_time = null;
      if($request->has('course_minutes')){
        //授業時間＋開始日時から終了日時を計算
        $form['course_minutes'] = $request->get('course_minutes');
        $end_time = date('Y/m/d H:i:s', strtotime($start_time.' +'.$form['course_minutes'].' minutes'));
      }
      if($request->has('end_hours') && $request->has('end_minutes')){
        $end_time = $form['start_date'].' '.$form['end_hours'].':'.$form['end_minutes'].':00';
      }
      $form['start_time'] = $start_time;
      $form['end_time'] = $end_time;
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
    unset($form['status']);
    return $form;
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

    //予定の指定
    if($request->has('start_date') && $request->has('start_hours')
        && $request->has('start_minutes') && $request->has('course_minutes')){
      $form['course_minutes'] = $request->get('course_minutes');
      $form['start_date'] = $request->get('start_date');
      $form['start_hours'] = $request->get('start_hours');
      $form['start_minutes'] = $request->get('start_minutes');
      $start_time = $form['start_date'].' '.$form['start_hours'].':'.$form['start_minutes'].':00';
      //授業時間＋開始日時から終了日時を計算
      $end_time = date('Y/m/d H:i:s', strtotime($start_time.' +'.$form['course_minutes'].' minutes'));
      $form['start_time'] = $start_time;
      $form['end_time'] = $end_time;
    }
    $form['charge_subject'] = $request->get('charge_subject');
    $form['english_talk_lesson'] = $request->get('english_talk_lesson');
    $form['piano_lesson'] = $request->get('piano_lesson');
    $form['kids_lesson'] = $request->get('kids_lesson');
    $form['lesson'] = $request->get('lesson');
    $form['course_type'] = $request->get('course_type');
    $form['place'] = $request->get('place');
    $form['is_exchange'] = false;
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
    $form['teacher_user_id'] = $teacher->user_id;

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

    $form['exchanged_calendar_id'] = 0;
    //内容の指定
    if($request->has('exchanged_calendar_id') && $request->get('exchanged_calendar_id') > 0){
      $form['exchanged_calendar_id'] = $request->get('exchanged_calendar_id');
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
    //$user = User::where('id', 607)->first()->details();
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'login_user' => $user,
      'remind' => false,
      'token' => $this->create_token(1728000),    //token期限＝20日
      'teacher_id' => $request->teacher_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'access_key' => $request->key,
      'cancel_reason' => $request->cancel_reason,
      'rest_reason' => $request->rest_reason,
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_status'=>$request->status,
      'search_work' => $request->search_work,
      'search_place' => $request->search_place,
    ];
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
        if($this->is_manager($user->role)===false){
          if($item->is_access($user->user_id)===false){
            abort(403, 'このページにはアクセスできません(1)');
          }
        }
      }
      else {
        abort(403, 'このページにはアクセスできません(2)');
      }

      $ret['item'] = $item->details($user->user_id);
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
      $page_data = $this->get_pagedata($_table["count"] , $param['_line'], $param["_page"]);
      foreach($page_data as $key => $val){
        $param[$key] = $val;
      }
      return view($this->domain.'.lists', $_table)
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
      if(!isset($user)) return $this->forbidden();

      $items = $this->model();
      if($this->is_student_or_parent($user->role)){
        $items = $items->where('status', '!=', 'new');
      }
      $items = $this->_search_scope($request, $items);
      if($user_id > 0)  $items = $items->findUser($user_id);
      $items = $this->_search_pagenation($request, $items);
      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $item = $item->details($user_id);
        if($user_id > 0) {
          $item->own_member = $item->get_member($user_id);
        }
      }

      return $this->api_response(200, "", "", $items->toArray());
    }
    public function search(Request $request)
    {
      $user = $this->login_details($request);
      if(!isset($user)) return $this->forbidden();
      if($this->is_manager($user->role)!=true) return $this->forbidden();
      $items = $this->model();
      //設定ID
      if(isset($request->setting_id)){
        $items = $items->where('user_calendar_setting_id', $request->setting_id);
      }
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
          "label" => "日時",
          "link" => "show",
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
      //ID 検索
      if(isset($request->id)){
        $items = $items->where('id',$request->id);
      }
      //ステータス 検索
      if(isset($request->search_status)){
        $items = $items->findStatuses(explode(',', $request->search_status.','));
      }
      //ワーク 検索
      if(isset($request->search_work)){
        $_param = "";
        if(gettype($request->search_work) == "array") $_param  = $request->search_work;
        else $_param = explode(',', $request->search_work.',');
        $items = $items->findWorks($_param);
      }
      //場所 検索
      if(isset($request->search_place)){
        $_param = "";
        if(gettype($request->search_place) == "array") $_param  = $request->search_place;
        else $_param = explode(',', $request->search_place.',');
        $items = $items->findPlaces($_param);
      }
      //講師ID
      if(isset($request->teacher_id)){
        $teacher = Teacher::where('id',$request->teacher_id)->first();
        if(isset($teacher)) $items = $items->findUser($teacher->user_id);
      }
      //生徒ID
      if(isset($request->student_id)){
        $student = Student::where('id',$request->student_id)->first();
        if(isset($student)) {
          //振替元対象
          if(isset($request->exchange_target) && isset($request->lesson)){
            $items = $items->findExchangeTarget($student->user_id, $request->lesson);
          }
          else {
            $items = $items->findUser($student->user_id);
          }
        }
      }
      //更新取得
      if(isset($request->update)){
        $items = $items->where('updated_at','>',$request->update);
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
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        $items = $items->where(function($items)use($search_words){
          foreach($search_words as $_search_word){
            if(empty($_search_word)) continue;
            $_like = '%'.$_search_word.'%';
            $items->orWhere('remark','like',$_like);
          }
        });
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
      $param['fields'] = $this->show_fields($param['item']->work);
      if($request->has('user')){
        return view($this->domain.'.simplepage', ["subpage"=>'' ])->with($param);
      }
      return view($this->domain.'.page', [
        'action' => $request->get('action')
      ])->with($param);
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
            abort(404, 'ページがみつかりません(21)');
        }
      }
      if($request->has('user') && !$request->has('key')){
          abort(404, 'ページがみつかりません(31)');
      }
      if($request->has('user') && $request->has('key')){
        if(!$this->is_enable_token($request->get('key'))){
          abort(403, '有効期限が切れています(31)');
        }
      }
      $param = $this->get_param($request, $id);

      if($request->has('user') && $request->has('key')){
        if($param['item']['access_key'] != $request->get('key')){
            abort(403, '有効期限が切れています(41)');
        }
      }
      if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
      $param['fields'] = $this->show_fields($param['item']->work);
      $param['action'] = '';
      if($request->has('user')){
        return view($this->domain.'.simplepage', ["subpage"=>$status ])->with($param);
      }
      return view($this->domain.'.'.$status, [])->with($param);
    }
    /**
     * ステータス更新
     *
     * @param  \Illuminate\Http\Request  $request
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
      if($status=="remind"){
        $calendar->change(['access_key' => $param['token']]);
      }
      else {
        //remind以外はステータスの更新
        if($calendar->status != $status){
          $res = $this->_status_update($request, $param, $id, $status);
          $param['item'] = UserCalendar::where('id', $param['item']->id)->first();
        }
        else {
          //TODO: カレンダー更新が2重で動作することがある
          $is_send = false;
        }
        if($status=='rest_cancel'){
          //TODO : 取り消し依頼
          $student = Student::where('id', $request->student_id)->first();
          $member = $calendar->members->where('user_id', $student->user_id)->first();
          $ask = $member->rest_cancel_ask();
        }
      }
      $slack_type = 'error';
      $slack_message = '更新エラー';


      if($this->is_success_response($res)){
        $slack_type = 'info';
        $slack_message = $this->status_update_message[$status];
        switch($status){
          case "rest":
            //お休み連絡メール通知
            if($is_send) $this->rest_mail($param);
            break;
          case "rest_cancel":
            //お休み取り消し連絡メール通知
            if($is_send) $this->rest_cancel_mail($param);
            break;
          case "confirm":
            //授業追加確認メール通知
            if($is_send) $this->confirm_mail($param);
            break;
          case "cancel":
            //授業キャンセル連絡メール通知
            if($is_send) $this->cancel_mail($param);
            break;
          case "fix":
            //授業追加確定メール通知
            if($is_send) $this->fix_mail($param);
            break;
          case "absence":
            //欠席メール通知
            if($is_send) $this->absence_mail($param);
            break;
          case "remind":
            //メール通知の再送
            $param['remind'] = true;
            if($param['item']['status']==='fix'){
              $this->fix_mail($param);
            }
            else if($param['item']['status']==='confirm'){
              $this->confirm_mail($param);
            }
            else if($param['item']['status']==='rest'){
              $this->rest_mail($param);
            }
            else if($param['item']['status']==='new'){
              $this->new_mail($param);
            }
            break;
        }
      }
      if($status==="remind"){
        $this->send_slack('カレンダーリマインド['.$param['item']['status'].']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', 'カレンダーリマインド');
      }
      else {
        $this->send_slack('カレンダーステータス更新[mail='.$is_send.']['.$status.']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', 'カレンダーステータス更新');
      }
      if($request->has('user')){
        $param['result'] = $this->status_update_message[$status];
        $param['fields'] = $this->show_fields($param['item']->work);
        return $this->save_redirect($res, $param, $this->status_update_message[$status], '/calendars/'.$param['item']['id'].'?user='.$param['user']->user_id.'&key='.$param['token']);
      }
      else {
        return $this->save_redirect($res, $param, $this->status_update_message[$status]);
      }
    }

    /**
     * カレンダーステータス更新
     *
     * @param  array  $param
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    private function _status_update(Request $request, $param, $id, $status){
      $res = $this->transaction(function() use ($request, $param, $id, $status){
        $form = $request->all();
        $param['item'] = UserCalendar::where('id', $param['item']->id)->first();
        $members = $param['item']->members;
        $_remark = '';
        if($status==='cancel'){
          $_remark = $request->get('cancel_reason');
        }
        else if($status==='rest'){
          $_remark = $request->get('rest_reason');
        }

        foreach($members as $member){
          //メンバーステータスの個別指定がある場合
          if(isset($form['is_all_student']) && $form['is_all_student']==1){
            //全生徒指定がある場合
            $member->update(['status' => $status, 'remark' => $_remark]);
          }
          else if(!empty($form[$member->id.'_status'])){
            $member->update(['status' => $form[$member->id.'_status'], 'remark' => $_remark]);
          }
        }

        //操作者のステータス更新
        $member_user_id = $param['user']->user_id;

        if($this->is_manager($param['user'])){
          //事務による代理登録=カレンダー主催者（講師）のステータスを更新
          $member_user_id = $param['item']->user_id;
        }

        if(!empty($param['student_id'])){
          //保護者の場合は、student_id指定がある場合
          $student = Student::where('id', $param['student_id'])->first();
          if(isset($student)) $member_user_id = $student->user_id;
        }
        UserCalendarMember::where('calendar_id', $param['item']->id)
            ->where('user_id', $member_user_id)
            ->update(['status'=>$status, 'remark' => $_remark]);

        $update_form = [
          'status'=>$status,
          'access_key' => '',
        ];
        $update_form['access_key'] = $param['token'];
        $param['item'] = $param['item']->change($update_form);
        return $param['item'];
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
        $param['fields'] = $this->show_fields($param['item']->work);
        return $this->save_redirect($res, $param, $this->domain_name.'を更新しました', '/calendars/'.$param['item']['id'].'?user='.$param['user']->user_id.'&key='.$param['token']);
      }
      else {
        return $this->save_redirect($res, $param, $this->domain_name.'を更新しました');
      }
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $res = $this->transaction(function() use ($request,$id){
        $item = $this->model()->where('id',$id)->first();
        $item->change($this->update_form($request, $id));
        return $item;
      }, '授業予定更新', __FILE__, __FUNCTION__, __LINE__ );

      return $res;
    }
    /**
     * 休み連絡通知メール送信
     * @param  Array  $param
     * @return boolean
     */
    private function rest_mail($param){
      return  $this->calendar_mail($param,
               'お休み連絡',
               'calendar_rest');
    }
    private function rest_cancel_mail($param){
      return $param['item']->teacher_mail('休み取り消し連絡', $param, 'text', 'calendar_rest_cancel');
    }
    private function cancel_mail($param){
      //TODO 講師あてにキャンセル連絡が届くが、グループレッスンの場合、部分的なキャンセル→全体をキャンセルが必要
      //return $param['item']->teacher_mail('授業予定のキャンセル', $param, 'text', 'calendar_cancel');
      return  $this->calendar_mail($param,
               '授業予定のキャンセル',
               'calendar_cancel');

    }
    private function confirm_mail($param){
      return  $this->calendar_mail($param,
               '授業予定のご確認',
               'calendar_confirm');

    }
    private function fix_mail($param){
      return $param['item']->teacher_mail('授業予定確定のご連絡', $param, 'text', 'calendar_fix');
      return  $this->calendar_mail($param,
               '授業予定確定のご連絡',
               'calendar_fix');

    }
    private function absence_mail($param){
      return  $this->calendar_mail($param,
               '授業欠席となりました',
               'calendar_absence');

    }
    private function new_mail($param){
      return $param['item']->teacher_mail('授業予定のご連絡', $param, 'text', 'calendar_new');

    }
    private function delete_mail($param){
      return $param['item']->teacher_mail('授業予定削除', $param, 'text', 'calendar_delete');

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
             'token' => $param['token'],
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
         $template);
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
          else {
            $param['item']->work = 9;
          }
        }
        if(!isset($param['teacher_id'])) {
          $param["teachers"] = Teacher::findStatuses('0')->get();
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
      if($request->has('course_minutes')){
        $param['item']['course_minutes'] = $request->get('course_minutes');
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
      return $this->save_redirect($res, $param, 'カレンダーに予定を登録しました');
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
        if(empty($form['start_time']) || empty($form['end_time'])) {
          abort(400, "日時パラメータ不正");
        }

        $calendar = UserCalendar::add($form);
        //生徒をカレンダーメンバーに追加
        if(!empty($form['students'])){
          foreach($form['students'] as $student){
            $calendar->memberAdd($student->user_id, $form['create_user_id']);
          }
        }
        $calendar = $calendar->details();
        $this->send_slack('カレンダー追加/ id['.$calendar['id'].'] status['.$calendar['status'].'] 開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', 'カレンダー追加');
        $param = $this->get_param($request, $calendar->id);
        $this->new_mail($param);
        return $calendar;
      }, '授業予定作成', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    /**
     * 授業予定削除処理
     *
     * @return \Illuminate\Http\Response
     */
    public function _delete(Request $request, $id)
    {
      $res = $this->transaction(function() use ($request, $id){
        $param = $this->get_param($request, $id);
        $user = $this->login_details($request);
        $calendar = $param["item"];
        $this->send_slack('カレンダー削除/ id['.$calendar['id'].'] status['.$calendar['status'].'] 開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', 'カレンダー削除');
        $this->delete_mail($param);
        $calendar->dispose();
        return $calendar;
      }, 'カレンダー削除', __FILE__, __FUNCTION__, __LINE__ );
    }


}
