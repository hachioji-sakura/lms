<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Ask;
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
  public function show_fields($type){
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
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'login_user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'access_key' => $request->key,
      'search_word'=>$request->search_word,
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_status'=>$request->status,
    ];

    if(!isset($user)) {
      if($request->has('key') && $this->is_enable_token($ret['access_key'])){
        $user = User::where('access_key', $ret['access_key'])->first();
        if(!isset($user)){
          abort(404);
        }
        $user = $user->details();
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
      $item = $this->model()->where('id','=',$id)->first();
      if(!isset($item)){
        abort(404);
      }
      $ret['item'] = $item->details();
      if($this->is_manager($user->role)!=true &&
        $ret['item']->target_user_id != $user->user_id &&
        $ret['item']->charge_user_id != $user->user_id ){
          abort(403);
      }
    }
    return $ret;
  }

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
    if($param['item']->type =="agreement"){
      $message = "";
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
      $res = $this->transaction($request, function() use ($request, $param, $id, $status){      $form = $request->all();
      $param['item'] = Ask::where('id', $param['item']->id)->first();
      $param['item'] = $param['item']->change([
        'status'=>$status,
        'login_user_id' => $param['user']->user_id,
      ]);
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
    if($res["data"]==null){
      $res = $this->error_response("同じ内容がすでに登録されています。");
    }
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
     $param['access_key'] = $param['trial']->parent->user->access_key;
     $param['action'] = '';
     return view('asks.hope_to_join', [])->with($param);
   }
   public function agreement_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');

     $param['fields'] = $this->show_fields($param['item']->type);
     $param['trial'] = $param['item']->get_target_model_data();
     $param['access_key'] = $param['trial']->parent->user->access_key;
     $param['action'] = '';
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
}
