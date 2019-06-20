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
  public $domain_name = '依頼';
  public $status_update_message = [
          'new' => '新規依頼を登録しました',
          'commit' => '依頼を承認しました',
          'cancel' => '依頼を差戻しました',
        ];
  public $list_fields = [
    'end_dateweek' => [
      'label' => '締切',
    ],
    'type_name' => [
      'label' => '依頼',
      'link' => 'show',
    ],
    'status_name' => [
      'label' => 'ステータス',
    ],
    'target_user_name' => [
      'label' => '依頼者',
    ],
    'charge_user_name' => [
      'label' => '担当',
    ],
  ];
  public function model(){
    return Ask::query();
  }
  public function show_fields(){
    $ret = [
      'type_name' => [
        'label' => '依頼',
        'size' => 6,
      ],
      'status_name' => [
        'label' => 'ステータス',
        'size' => 6,
      ],
      'end_dateweek' => [
        'label' => '期限',
      ],
      'charge_user_name' => [
        'label' => '担当者',
        'size' => 6,
      ],
      'target_user_name' => [
        'label' => '対象者',
        'size' => 6,
      ],
      'body' => [
        'label' => '備考',
      ],
    ];
    return $ret;
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    if(!isset($user)) {
      abort(403);
    }
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'login_user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_status'=>$request->status,
    ];

    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      $ret['item'] = $item->details();
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
    $param['fields'] = $this->show_fields($param['item']->work);
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
    return $this->save_redirect($res, $param, $this->status_update_message[$status]);
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
      $param['item'] = Ask::where('id', $param['item']->id)->first();
      $param['item'] = $param['item']->change([
        'status'=>$status,
        'login_user_id' => $param['user']->user_id,
      ]);
      return $param['item'];
    }, 'カレンダーステータス更新', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }
  public function _store(Request $request)
  {
    $res = $this->transaction(function() use ($request){
      $form = $request->all();
      $param = $this->get_param($request);
      $form["create_user_id"] = $param["user"]->user_id;
      $item = Ask::add($form['type'], $form);
      return $item;
    }, $this->domain_name, __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }
   public function teacher_chagne_page(Request $request, $id)
   {
     $param = $this->get_param($request, $id);

     if(!isset($param['item'])) abort(404, 'ページがみつかりません(32)');
     $param['fields'] = $this->show_fields($param['item']->work);
     $param['action'] = '';
     return view('calendars.teacher_change', [])->with($param);
   }
}
