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
          'presence' => '授業を出席に更新しました。',
          'absence' => '授業を欠席に更新しました。',
          'remind' => '授業予定の確認連絡をしました。',
        ];
  public function model(){
    return UserCalendar::query();
  }
  public function show_fields(){
    $user = $this->login_details();
      $ret = [
      'datetime' => [
        'label' => '日時',
        'size' => 6,
      ],
      'status_name' => [
        'label' => 'ステータス',
        'size' => 6,
      ],
      'teacher_name' => [
        'label' => '講師',
        'size' => 6,
      ],
      'place' => [
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

  /**
   * 更新用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function update_form(Request $request){
    $form = [];
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
   * 新規登録用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function create_form(Request $request){
    $user = $this->login_details();
    $form = [];
    $form['create_user_id'] = $user->user_id;

    //予定の指定
    if($request->has('start_date') && $request->has('start_hours')
        && $request->has('start_minutes') && $request->has('lesson_time')){
      $form['lesson_time'] = $request->get('lesson_time');
      $form['start_date'] = $request->get('start_date');
      $form['start_hours'] = $request->get('start_hours');
      $form['start_minutes'] = $request->get('start_minutes');
      $form['is_exchange'] = false;
      $start_time = $form['start_date'].' '.$form['start_hours'].':'.$form['start_minutes'].':00';
      $end_time = date('Y/m/d H:i:s', strtotime($start_time.' +'.$form['lesson_time'].' minutes'));
      $form['start_time'] = $start_time;
      $form['end_time'] = $end_time;
    }
    else {
      abort(400);
    }

    //生徒と講師の情報が予定追加時には必須としている
    //講師の指定
    if($request->has('teacher_id')){
      $form['teacher_id'] = $request->get('teacher_id');
    }
    else if($this->is_teacher($user->role)){
      //ログインユーザーが講師の場合はteacher_id = 自分
      $form['teacher_id'] = $user->user_id;
    }
    else {
      abort(400, "講師指定なし");
    }
    $teacher = Teacher::where('id', $form['teacher_id'])->first();
    if(!isset($teacher)){
      //講師が存在しない
      abort(400, "存在しない講師");
    }
    $teacher_lesson = UserTag::findUser($teacher->user_id)->findKey('lesson');
    if(!isset($teacher_lesson)){
      abort(500, "講師のLessonが設定されていない");
    }
    //生徒の指定
    if($request->has('student_id')){
      $form['student_id'] = $request->get('student_id');
    }
    else {
      abort(400, "生徒指定なし");
    }
    $student = Student::where('id', $form['student_id'])->first();
    if(!isset($student)){
      //生徒が存在しない
      abort(400, "存在しない生徒");
    }

    if($request->has('lesson')){
      $form['lesson'] = $request->get('lesson');
    }

    //内容の指定
    if($request->has('course') && $request->has('subject') && $request->has('place')){
      $form['course'] = $request->get('course');
      $form['subject'] = $request->get('subject');
      $form['place'] = $request->get('place');
      $form['work'] = 6;
    }
    else {
      abort(400, "パラメータ指定エラー");
    }
    $form['exchanged_calendar_id'] = 0;
    //内容の指定
    if($request->has('add_type') && $request->get('add_type') == "exchange"){
      if($request->has('exchanged_calendar_id') && $request->get('exchanged_calendar_id') > 0){
        $form['exchanged_calendar_id'] = $request->get('exchanged_calendar_id');
      }
      else {
        abort(400, "振替元IDの指定がない");
      }
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
    $user = $this->login_details();
    //$user = User::where('id', 607)->first()->details();
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'remind' => false,
      'token' => $this->create_token(1728000),    //token期限＝20日
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'search_status'=>$request->status,
      'access_key' => $request->key,
      'cancel_reason' => $request->cancel_reason,
      'rest_reason' => $request->rest_reason,
      'attributes' => $this->attributes(),
    ];
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
      $param = $this->get_param($request);
      $items = DB::table($this->table)->get();
      return $items->toArray();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function api_index(Request $request, $user_id=0, $from_date=null, $to_date=null)
    {
      $param = $this->get_param($request);
      $user = $this->login_details();
      if(!isset($user)) return $this->forbidden();
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
      $_table = $this->search($request, $user_id);

      return $this->api_response(200, "", "", $_table);
    }

    /**
     * 検索～一覧
     *
     * @param  \Illuminate\Http\Request  $request
     * @return [Collection, field]
     */
    public function search(Request $request, $user_id=0)
    {
      $user = $this->login_details();

      $items = $this->model();
      if($this->is_student_or_parent($user->role)){
        $items = $items->where('status', '!=', 'new');
      }
      $items = $this->_search_scope($request, $items);
      if($user_id > 0)  $items = $items->findUser($user_id);
      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $item = $item->details($user_id);
        if($user_id > 0) {
          $item->own_member = $item->get_member($user_id);
        }
      }
      return $items->toArray();
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
        $statuses = explode(',', $request->search_status.',');
        $items = $items->findStatuses($statuses);
      }
      //振替元対象
      if(isset($request->exchange_target)){
        $items = $items->findExchangeTarget();
      }
      //講師ID
      if(isset($request->teacher_id)){
        $teacher = Teacher::where('id',$request->teacher_id)->first();
        if(isset($teacher)) $items = $items->findUser($teacher->user_id);
      }
      //生徒ID
      if(isset($request->student_id)){
        $student = Student::where('id',$request->student_id)->first();
        if(isset($student)) $items = $items->findUser($student->user_id);
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
      $items = $items->rangeDate($from_date, $to_date);
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
      $param['fields'] = $this->show_fields();
      if($request->has('user')){
        return view($this->domain.'.simplepage', ["subpage"=>'' ])->with($param);
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
    public function show_calendar(Request $request, $user_id)
    {
      $param = $this->get_param($request, "");
      $param["date"] = "2019-04-29";
      $param["minHour"] = "10";
      $param["maxHour"] = "15";
      $param["user_id"] = $user_id;
      return view($this->domain.'.user_calendar', [
      ])->with($param);
    }
    /**
     * カレンダーステータス更新ページ
     *
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function status_update_page(Request $request, $id, $status)
    {
      if(!$request->has('user')){
        if (!View::exists($this->domain.'.'.$status)) {
            abort(404, 'ページがみつかりません(2)');
        }
      }
      if($request->has('user') && !$request->has('key')){
          abort(404, 'ページがみつかりません(3)');
      }
      if($request->has('user') && $request->has('key')){
        if(!$this->is_enable_token($request->get('key'))){
          abort(403, '有効期限が切れています(3)');
        }
      }
      $param = $this->get_param($request, $id);

      if($request->has('user') && $request->has('key')){
        if($param['item']['access_key'] != $request->get('key')){
            abort(403, '有効期限が切れています(4)');
        }
      }
      if(!isset($param['item'])) abort(404, 'ページがみつかりません(3)');

      $param['fields'] = $this->show_fields();
      $param['action'] = '';

      if($request->has('user')){
        return view($this->domain.'.simplepage', ["subpage"=>$status ])->with($param);
      }
      return view($this->domain.'.'.$status, [ ])->with($param);
    }
    /**
     * カレンダーステータス更新
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
      if($status=="remind"){
        $calendar = UserCalendar::where('id', $id)->first();
        $calendar->change(['access_key' => $param['token']]);
      }
      else {
        //remind以外はステータスの更新
        if($param['item']->status != $status){
          $res = $this->_status_update($request, $param, $id, $status);
          $param['item'] = UserCalendar::where('id', $param['item']->id)->first();
        }
        else {
          //TODO: カレンダー更新が2重で動作することがある
          $is_send = false;
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
            break;
        }
      }
      if($status==="remind"){
        $this->send_slack('カレンダーリマインド['.$param['item']['status'].']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', 'カレンダーリマインド');
      }
      else {
        $this->send_slack('カレンダーステータス更新[maile='.$is_send.']['.$status.']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', 'カレンダーステータス更新');
      }
      if($request->has('user')){
        $param['result'] = $this->status_update_message[$status];
        $param['fields'] = $this->show_fields();
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
      $form = $request->all();
      try {
        DB::beginTransaction();
        //カレンダーステータス変更
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
          if(!empty($form[$member->id.'_status'])){
            $member->update(['status' => $form[$member->id.'_status'], 'remark' => $_remark]);
          }
        }
        $member_user_id = $param['user']->user_id;
        if(!empty($param['student_id'])){
          $student = Student::where('id', $param['student_id'])->first();
          if(isset($student)) $member_user_id = $student->user_id;
        }
        //保護者以外の場合
        UserCalendarMember::where('calendar_id', $param['item']->id)
            ->where('user_id', $member_user_id)
            ->update(['status'=>$status, 'remark' => $_remark]);

        $update_form = [
          'status'=>$status,
          'access_key' => '',
        ];
        $update_form['access_key'] = $param['token'];
        $param['item'] = $param['item']->change($update_form);
        //カレンダーメンバーステータス変更
        DB::commit();
        return $this->api_response(200, '', '', $param['item']);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
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
      }
      if($request->has('status')){
        return $this->status_update($request, $id, $request->get('status'));
      }

      if($request->has('user')){
        $param['result'] = $this->status_update_message[$status];
        $param['fields'] = $this->show_fields();
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
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $item = $this->model()->where('id',$id)->change($this->update_form($request));
        DB::commit();
        return $this->api_response(200, '', '', $item);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
    }
    /**
     * 休み連絡通知メール送信
     * @param  Array  $param
     * @return boolean
     */
    private function rest_mail($param){
      return  $this->_mail($param,
               'お休み連絡',
               'calendar_rest');

    }
    private function cancel_mail($param){
      return  $this->_mail($param,
               '授業予定のキャンセル',
               'calendar_cancel');

    }
    private function confirm_mail($param){
      return  $this->_mail($param,
               '授業予定のご確認',
               'calendar_confirm');

    }
    private function fix_mail($param){
      return  $this->_mail($param,
               '授業予定確定のご連絡',
               'calendar_fix');

    }
    private function absence_mail($param){
      return  $this->_mail($param,
               '授業欠席となりました',
               'calendar_absence');

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
    private function _mail($param, $title, $template){
      $item = $param['item']->details();
      $login_user = $param['user'];
      $target_student_id = $param['student_id'];
      $send_from = "student";
      if($this->is_teacher($login_user->role)){
        $send_from = "teacher";
      }
      else if($this->is_manager($login_user->role)){
        $send_from = "manager";
      }
      $members = $item->members;
      if($param['remind']===true){
        //$title .= '【再送】';
      }
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
            $email = $relation->parent->user->email.';';
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
       $is_exist = false;
       foreach($send_dataset as $i => $send_data){
          if($email ===$send_data['email']){
            //アドレス追加済み
            $is_exist = true;
            //$send_dataset[$i]['student_ids'][] = $student_id;
            break;
          }
       }
       $user = User::where('id', $user_id)->first();
       if(isset($user)) $user = $user->details();
       if($is_exist==false){
         $_item = UserCalendar::where('id', $item->id)->first()->details($user_id);
         if(!empty($param['cancel_reason'])){
           $_item['cancel_reason'] = $param['cancel_reason'];
         }
         if(!empty($param['rest_reason'])){
           $_item['rest_reason'] = $param['rest_reason'];
         }
         if($param['student_id'] > 0){
           $_item['target_student'] = Student::where('id', $param['student_id'])->first();
         }
         $send_dataset[] = [
           "email" => $email,
           'send_to' => $send_to,
           'item' => $_item,
           'token' => $param['token'],
           'user' => $user
         ];
       }
      }

      foreach($send_dataset as $send_data){
        $this->send_mail($send_data['email'],
         $title,
         [
         'user' => $send_data['user'],
         'send_to' => $send_data['send_to'],
         'item' =>$send_data['item'],
         'token' => $send_data['token'],
         'user_id' => $user_id,
         ],
         'text',
         $template);
      }

      return true;
    }
    /**
     * 生徒取得
     *
     * @param  array  $param
     * @return array
     */
    private function get_students($param){
      $items = Student::whereRaw('students.user_id in (select id from users where status !=9)');
      if($this->is_teacher($param['user']->role)){
        $items = $items->whereRaw('students.id in (select student_id from charge_students where teacher_id=?)', $param['user']->id);
      }
      else if($param['teacher_id'] > 0){
        $items = $items->whereRaw('students.id in (select student_id from charge_students where teacher_id=?)', $param['teacher_id']);
      }
      $items = $items->get();
      return $items;
    }
    /**
     * 講師取得
     *
     * @param  array  $param
     * @return array
     */
    private function get_teachers($param){
      $items = Teacher::whereRaw('teachers.user_id in (select id from users where status!=9)');
      if($this->is_teacher($param['user']->role)){
        $items = $items->where('id',  $param['user']->id);
      }
      else if($param['teacher_id'] > 0){
        $items = $items->where('id',  $param['teacher_id']);
      }
      $items = $items->get();
      return $items;
    }

    /**
     * 新規登録画面
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Request $request)
   {
      $param = $this->get_param($request);
      $start_date = date('Y/m/d');
      if($request->has('start_date')){
        $start_date = date('Y/m/d', strtotime($request->get('start_date')));
      }
      $param['item'] = [
        'lesson_time' => $request->get('lesson_time'),
        'start_date' => $start_date,
        'start_hours' => $request->get('start_hours'),
        'start_minutes' => $request->get('start_minutes'),
      ];
      $param['students'] = $this->get_students($param);
      $param['teachers'] = [];
      if(!$request->has('teacher_id')){
        //teacher_id指定がない場合、講師は選択する
        $param['teachers'] = $this->get_teachers($param);
        if(count($param['teachers'])==1){
          $param['item']['teacher_id'] = $param['teachers'][0]['id'];
        }
      }
      else {
        $param['item']['teacher_id'] = $request->get('teacher_id');
      }

      return view($this->domain.'.create',
        [ 'error_message' => ''])
        ->with($param);
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

      return $this->save_redirect($res, $param, 'カレンダーに予定を登録しました');
    }
    /**
     * 新規登録ロジック
     *
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
      $form = $this->create_form($request);
      try {
        DB::beginTransaction();
        $teacher = Teacher::where('id', $form['teacher_id'])->first();
        $form['teacher_user_id'] = $teacher->user_id;
        $calendar = UserCalendar::add($form);
        //生徒をカレンダーメンバーに追加
        if(!empty($form['student_id'])){
          $student = Student::where('id', $form['student_id'])->first();
          $calendar->memberAdd($student->user_id, $form['create_user_id']);
        }
        $calendar = $calendar->details();
        $this->send_slack('カレンダー追加/ id['.$calendar['id'].']開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', 'カレンダー追加');
        DB::commit();
        return $this->api_response(200, '', '', $calendar);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
    }
    /**
     * 授業予定削除処理
     *
     * @return \Illuminate\Http\Response
     */
    public function _delete(Request $request, $id)
    {
      $res = $this->transaction(function() use ($request, $id){
        $user = $this->login_details();
        $item = $this->model()->where('id',$id)->first();
        $item->dispose();
        return $item;
      }, 'カレンダー削除', __FILE__, __FUNCTION__, __LINE__ );
    }

    public function test(Request $request){
      $setting = UserCalendarSetting::where('id', 258)->first();
      $res = $this->api_response();
      $res = $setting->setting_to_calendar();
      return $res;
    }

}
