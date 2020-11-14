<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\EventUserController;
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
    if(!$request->has('search_status')) $ret['search_status'] = 'new,progress';
    return $ret;
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
      $items = $items->findStatuses($request->search_status);
    }

    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      $items = $items->where(function($items)use($search_words){
        foreach($search_words as $_search_word){
          if(empty($_search_word)) continue;
          $_like = '%'.$_search_word.'%';
          $items->orWhere('body','like',$_like)->orWhere('title','like',$_like);
        }
      });
    }

    return $items;
  }
  public function show_fields($type=''){
    $ret = [
      'title' => [
        'label' => '件名',
        'size' => 6,
      ],
      'status_name' => [
        'label' => 'ステータス',
        'size' => 6,
      ],
      'event_term' => [
        'label' => '開催期間'
      ],
      'response_term' => [
        'label' => '回答期間'
      ],
      'body' => [
        'label' => '内容'
      ],
      'template_title' => [
        'label' => 'テンプレート',
        'size' => 6,
      ],
      'create_user_name' => [
        'label' => '作成者',
        'size' => 6,
      ],
    ];
    return $ret;
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
       'label' => '件名',
       'link' => 'show'
     ],
     /*

     'response_term' => [
       'label' => '回答期間'
     ],
     'create_user_name' => [
       'label' => '作成者'
     ],
     */
     'status_name' => [
       'label' => 'ステータス'
     ],
     'event_user_count' => [
       'label' => '対象者数'
     ],
     'event_term' => [
       'label' => '開催期間'
     ],
     'event_user_count' => [
       'label' => '対象者数',
       "link" => function($row){
         return "/event_users?event_id=".$row['id'];
       }
     ],
   ];
   $fields['buttons'] = [
     'label' => '操作',
     'button' => [
       'edit',
       'delete',
       "send_mail" => [
         "method" => "send_mail",
         "label" => "一斉送信",
         "style" => "outline-primary",
       ],
     ]
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
   public function send_mail_page(Request $request , $id){
     $param = $this->get_param($request, $id);
     $param['fields'] = $this->show_fields('');

     return view($this->domain.'.send_mail', [])
     ->with($param);
   }
}
