<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Ask;
use Illuminate\Support\Facades\Auth;

use DB;
use View;
class AskController extends MilestoneController
{
  public $domain = 'asks';
  public $table = 'asks';
  public $status_update_message = [
          'new' => '新規依頼を登録しました。',
          'commit' => '依頼を承認しました。',
          'cancel' => '依頼を取り消しました。',
          'remind' => '依頼を再送しました。',
        ];
  public function model(){
    return Ask::query();
  }
  public function list_fields(){
    $ret = [
      'end_dateweek' => [
        'label' => __('labels.limit'),
      ],
      'type_name' => [
        'label' => __('labels.ask_type'),
        'link' => 'show',
      ],
      'status_name' => [
        'label' => __('labels.status'),
      ],
      'target_user_name' => [
        'label' => __('labels.target_user'),
      ],
      'charge_user_name' => [
        'label' => __('labels.charge_user'),
      ],
    ];
    return $ret;
  }
  public function show_fields($type=''){
    $ret = [
      'type_name' => [
        'label' => __('labels.asks'),
        'size' => 6,
      ],
      'status_name' => [
        'label' => __('labels.status'),
        'size' => 6,
      ],
    ];
    switch($type){
      case 'recess':
        $ret['duration'] = [
         'label' => __('labels.recess').__('labels.duration'),
        ];
        break;
      case 'unsubscribe':
        $ret['start_date'] = [
         'label' => __('labels.unsubscribe').__('labels.day'),
       ];
       break;
      default:
      /*
        $ret['end_dateweek'] = [
         'label' => __('labels.limit'),
       ];
       */
    }
    $ret2 =[
      'charge_user_name' => [
        'label' => __('labels.charge_user'),
        'size' => 6,
      ],
      'target_user_name' => [
        'label' => __('labels.target_user'),
        'size' => 6,
      ],
      'body' => [
        'label' => __('labels.body'),
      ],
    ];
    $ret = array_merge($ret, $ret2);
    return $ret;
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    //ログインしていない場合でも利用するケースがある
    $ret = $this->get_common_param($request, false);
    if(!isset($user)) {
      if($request->has('key') && $this->is_enable_token($ret['access_key'])){
        $ask = $this->model()->where('access_key', $ret['access_key'])->first();
        if(!isset($ask)){
          abort(404);
        }
        $user = $ask->target_user->details();
        $ret['user'] = $user;
      }
      else {
        abort(403, 'このページへの操作期限が切れています');
      }
    }
    if(!isset($user)) {
      abort(403);
    }

    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id', $id)->first();
      if(!isset($item)){
        abort(404);
      }
      $ret['item'] = $item->details();
      if($item->is_access($user->user_id)==false){
          abort(403);
      }
    }
    return $ret;
  }

  public function index(Request $request)
  {
    $param = $this->get_param($request);
    $_table = $this->search($request);
    $param['items'] = $_table['items'];
    $param['fields'] = [
      'title' => [
        'label' => '概要',
        'link' => 'show',
      ],
      'target_user_name' => [
        'label' => '対象者',
        'link' => function($row){
          return $row->target_user->details()->domain."/".$row->target_user->details()->id;
        }
      ],
      'status_name' => [
        'label' => 'ステータス',
      ],
      'type_name' =>[
        'label' => '依頼種別',
      ],
      'body' => [
        'label' => '内容'
      ],
      'buttons' => [
        'label' => '操作',
        'button' => [
          'commit' => [
            'label' => '完了にする',
            'style' => 'danger',
            'method' => 'status_update/commit',
            'type' => function($row){
                if($row->status == 'new'){
                  return true;
                }else{
                  return false;
                }
              }
          ],
        ],
      ],
    ];
    return view($this->domain.'.lists')->with($param);
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
    $param = $this->get_param($request, $id);

    if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
    $param['fields'] = $this->show_fields($param['item']->type);
    $param['action'] = '';
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
    $ask = Ask::where('id', $id)->first();

    if($request->has('status') && $status!=$request->get('status')){
      $status = $request->get('status');
    }

    if($status!="remind"){
      //remind以外はステータスの更新
      if($ask->status != $status){
        $res = $this->_status_update($request, $param, $id, $status);
        $param['item'] = Ask::where('id', $param['item']->id)->first();
      }
      else {
        $is_send = false;
      }
    }
    $slack_type = 'error';
    $slack_message = '更新エラー';

    if($this->is_success_response($res)){
      $slack_type = 'info';
      $slack_message = $this->status_update_message[$status];
      switch($status){
        case "cancel":
        case "commit":
          //連絡メール通知
          if($is_send) $param['item']->remind_mail($param['user']->user_id);
          break;
        case "remind":
          //連絡メール通知
          $param['item']->remind_mail($param['user']->user_id, true);
          break;
      }
    }
    if($status==="remind"){
      $this->send_slack('依頼リマインド['.$param['item']['status'].']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', '依頼リマインド');
    }
    else {
      $this->send_slack('依頼ステータス更新[mail='.$is_send.']['.$status.']:'.$slack_message.' / id['.$param['item']['id'].']開始日時['.$param['item']['start_time'].']終了日時['.$param['item']['end_time'].']生徒['.$param['item']['student_name'].']講師['.$param['item']['teacher_name'].']', 'info', '依頼ステータス更新');
    }
    $message = $slack_message;

    //更新メッセージを表示しないようにする
    switch($param['item']->type){
      case "hope_to_join":
      case "agreement":
      case "agreement_confirm":
        $message = "";
        break;
    }
    return $this->save_redirect($res, $param, $message);
  }

  /**
   * カレンダーステータス更新
   *
   * @param  array  $param
   * @param  string  $status
   * @return \Illuminate\Http\Response
   */
  private function _status_update(Request $request, $param, $id, $status){
    $res = $this->transaction($request, function() use ($request, $param, $id, $status){
      $form = $request->all();
      $form['status'] = $status;
      $form['login_user_id'] = $param['user']->user_id;
      $param['item'] = Ask::where('id', $param['item']->id)->first();
      $param['item'] = $param['item']->change($form);
      return $this->api_response(200, '', '', $param['item']);
    }, '依頼ステータス更新', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }
  public function save_validate(Request $request)
  {
    $form = $request->all();
    $param = $this->get_param($request);

    switch($form['target_model']){
      case "students":
        //対象＝生徒への更新の場合、対象者＝保護者
        if($this->is_parent($param['user']->role)==false){
          $this->forbidden('この依頼は登録できません');
        }
        $target_model = Student::where('id',  $form['target_model_id'])->first();
        if(!isset($target_model)){
          $this->notfoubd('この依頼の対象者がみつかりません');
        }
        if($target_model->is_parent($param['user']->id)==false){
          $this->forbidden('この依頼は登録できません');
        }
        break;
      //対象＝事務 or 講師への更新の場合、対象者＝本人
      case "managers":
      case "teachers":
        $target_model = null;
        if($form['target_model']=='teachers'){
          $target_model = Teacher::where('id',  $form['target_model_id'])->first();
        }
        else {
          $target_model = Manager::where('id',  $form['target_model_id'])->first();
        }
        if(!isset($target_model)){
          $this->notfoubd('この依頼の対象者がみつかりません');
        }
        if($target_model->user_id != $param['user']->user_id){
          $this->forbidden('この依頼は登録できません');
        }
        break;
    }
    return $this->api_response(200, '', '');
  }
  public function _store(Request $request)
  {
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $res = $this->transaction($request, function() use ($request){
      $form = $request->all();
      $param = $this->get_param($request);
      $form["create_user_id"] = $param["user"]->user_id;
      $item = Ask::add($form);

      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    /*
    if($res["data"]==null){
      $res = $this->error_response("同じ内容がすでに登録されています。");
    }
    */
    switch ($res['data']->type){
      //代講以外の登録メッセージを変更する場合追加する
      case "teacher_change":
        $message = __('messages.info_teacher_change_send');
        break;
      default:
        $message = '登録しました。';
        break;
    }
    $res['message'] = $message;
    return $res;
   }
   public function destroy(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     $res = $this->_delete($request, $id);
     //生徒詳細からもCALLされる
     return $this->save_redirect($res, $param, '依頼を取り消しました');
   }

   public function _delete(Request $request, $id)
   {
     $form = $request->all();
     $res = $this->transaction($request, function() use ($request, $form, $id){
       $item = $this->model()->where('id',$id)->first();
       $item->dispose();
       return $this->api_response(200, '', '', $item);
     }, '依頼を取り消しました', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function teacher_chagne_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
     $param['fields'] = $this->show_fields($param['item']->type);
     $param['action'] = '';
     return view('calendars.teacher_change', [])->with($param);
   }
   public function hope_to_join_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
     $param['fields'] = $this->show_fields($param['item']->type);
     $param['trial'] = $param['item']->get_target_model_data();
     $param['access_key'] = $param['item']->access_key;;
     $param['action'] = '';
     return view('asks.hope_to_join', [])->with($param);
   }
   public function agreement_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');

     $param['fields'] = $this->show_fields($param['item']->type);
     $param['agreement'] = $param['item']->get_target_model_data();
     $param['access_key'] = $param['item']->access_key;
     $param['action'] = '';
     $param['fields'] = [];
     $param['is_money_edit'] = false;
     $param['student'] = $param['agreement']->student;
     return view('asks.agreement', [])->with($param);
   }

   public function commit_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
     if(!$request->has('user'))  abort(404, 'ページがみつかりません(33)');
     if($param['item']->charge_user_id != $request->get('user')) abort(403);
     $param['fields'] = $this->show_fields($param['item']->type);
     $param['action'] = '';
     return view('asks.simplepage', [])->with($param);
   }
   public function daily_proc(Request $request, $d='')
   {
     set_time_limit(600);

     if(empty($d)) $d = date('Y-m-d');
     $res = $this->transaction($request, function() use ($request, $d){
       $result = ['asks'=>[], 'recess'=>[],'unsubscribe'=>[]];
       //退会・休会の処理
       $asks = Ask::where('status', 'commit')->findTypes(['recess', 'unsubscribe'])
         ->where('start_date', $d)
         ->get();
       foreach($asks as $ask){
         $target_model_data = $ask->get_target_model_data();
         if($target_model_data==null) continue;
         $result['asks'][] = $ask;
         if($ask->type=="recess"){
           $ret = $target_model_data->recess();
         }
         else if($ask->type=="unsubscribe"){
           $ret = $target_model_data->unsubscribe();
         }
         if($ret!=null){
           if(isset($ret['user_calendar_members'])){
             $target_model_data['user_calendar_members'] = $ret['user_calendar_members'];
           }
           $result[$ask->type][] = $target_model_data;
           if($ask->type=="unsubscribe"){
             $ask->complete();
           }
         }
       }
       //休会再開の処理（終了日が昨日）
       $d = date("Y-m-d",strtotime("-1 day ".$d));

       $asks = Ask::where('status', 'commit')->findTypes(['recess'])
         ->where('end_date', $d)
         ->get();
       foreach($asks as $ask){
         $target_model_data = $ask->get_target_model_data();
         if($target_model_data==null) continue;
         $result['asks'][] = $ask;
         $ret = $target_model_data->recess_cancel();
         if($ret!=null){
           if(isset($ret['user_calendar_members'])){
             $target_model_data['user_calendar_members'] = $ret['user_calendar_members'];
           }
           if(isset($ret['conflict_calendar_members'])){
             $target_model_data['conflict_calendar_members'] = $ret['conflict_calendar_members'];
           }
           $result[$ask->type][] = $target_model_data;
           $ask->complete();
         }
       }
       return $this->api_response(200, '', '', $result);
     }, 'daily_proc', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit_date(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    return view($this->domain.'.edit_date', [
      '_edit' => true])
      ->with($param);
  }
  public function _update(Request $request, $id)
  {
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $res =  $this->transaction($request, function() use ($request, $id){
      $form = $request->all();
      $item = $this->model()->where('id', $id)->first();
      $is_file_delete = false;
      $fields = ['start_date', 'end_date'];
      $d = [];
      foreach($fields as $field){
        if(!isset($form[$field])) continue;
        $d[$field] = $form[$field];
      }
      $item->update($d);
      return $this->api_response(200, '', '', $item);
    }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }

}
