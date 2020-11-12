<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventTemplate;
//class EventController extends Controller
class EventController extends MilestoneController
{
  public $domain = 'events';
  public $table = 'events';
  public function model(){
    return Event::query();
  }

  public function get_param(Request $request, $id=null){
    $ret = parent::get_param($request, $id);
    $templates = EventTemplate::all();
    $ret['templates'] = $templates;
    return $ret;
  }
/**
 * イベント画面表示
 *
 */
 public function show(Request $request, $id)
 {
  $param = $this->get_param($request, $id);
  $fields = [
    'title' => [
      'label' => '件名'
    ],
    'event_term' => [
      'label' => '開催期間'
    ],
    'response_term' => [
      'label' => '回答期間'
    ],
    'body' => [
      'label' => '備考'
    ],
    'create_user_name' => [
      'label' => '作成者'
    ],
  ];
  return view('components.page', [
  'action' => $request->get('action'),
  'fields'=>$fields])
  ->with($param);

 }

 /**
  * 検索～一覧
  *
  * @param  \Illuminate\Http\Request  $request
  * @return [Collection, field]
  */
 public function search(Request $request)
 {
   $param = $this->get_param($request);
   $items = $this->model();
   $user = $this->login_details($request);
   $items = $this->_search_scope($request, $items);
   $items = $items->paginate($param['_line']);
   $fields = [
     'title' => [
       'label' => '件名'
     ],
     'template_title' => [
       'label' => 'テンプレート'
     ],
     'event_term' => [
       'label' => '開催期間'
     ],
     'response_term' => [
       'label' => '回答期間'
     ],
     'event_user_count' => [
       'label' => '対象者数'
     ],
     'create_user_name' => [
       'label' => '作成者'
     ],
   ];
   $fields['buttons'] = [
     'label' => '操作',
     'button' => ['edit', 'delete']
   ];

   return ['items' => $items, 'fields' => $fields];

 }

  public function save_validate(Request $request)
  {
    $form = $request->all();
    //保存時にパラメータをチェック
    if(empty($form['title']) || empty($form['response_from_date']) || empty($form['response_to_date']) ||
        empty($form['event_from_date']) || empty($form['event_to_date'])){
      return $this->bad_request('リクエストエラー', ''.$form['title']);
    }
    return $this->api_response(200, '', '');
  }
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['create_user_id'] = $user->user_id;
    $form['event_template_id'] = $request->get('event_template_id');
    $form['event_from_date'] = $request->get('event_from_date');
    $form['event_to_date'] = $request->get('event_to_date');
    $form['response_from_date'] = $request->get('response_from_date');
    $form['response_to_date'] = $request->get('response_to_date');
    $form['title'] = $request->get('title');
    $form['status'] = 'new';
    $form['body'] = htmlentities($request->get('body'), ENT_QUOTES, 'UTF-8');
    return $form;
  }
  /**
   * 新規登録ロジック
   *
   * @return \Illuminate\Http\Response
   */
  public function _store(Request $request)
  {
    $form = $this->create_form($request);
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $res = $this->transaction($request, function() use ($request, $form){
      $item = Event::add($form);
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }
}
