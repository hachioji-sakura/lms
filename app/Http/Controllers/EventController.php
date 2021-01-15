<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\EventUserController;
use App\Models\Event;
use App\Models\EventTemplate;
use App\Models\EventUser;
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
    if($request->has('id')){
      $items = $items->where('id',$request->id);
    }
    //ステータス 検索
    if($request->has('search_status')){
      $items = $items->findStatuses($request->search_status);
    }

    if($request->has('student_lesson_request')){
      $items = $items->studentLessonRequest();
    }

    //検索ワード
    if($request->has('search_word')){
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
        'label' => '開催期間',
        'size' => 6,
      ],
      'response_term' => [
        'label' => '回答期間',
        'size' => 6,
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
     'lesson_request_count' => [
       'label' => '新規申込数',
       'button_style' => 'primary',
       "link" => function($row){
         if(isset($row->event) && $row->event->is_need_request()==false) return '';
         if(count($row->lesson_requests->where('status', 'new')) == 0) return '';
         return "/events/".$row['id']."/lesson_requests";
       }
     ],
   ];
   $fields['buttons'] = [
     'label' => '操作',
     'button' => [
       'edit',
       'delete',
       "to_inform" => [
         "method" => "to_inform",
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
    //$form['status'] = 'new';
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
      $item = Event::add($form);
      return $this->api_response(200, '', '', $item);
      $res = $this->transaction($request, function() use ($request, $form){
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }
   public function _update(Request $request, $id)
   {
     $form = $this->create_form($request);
     $res = $this->save_validate($request);
     if(!$this->is_success_response($res)){
       return $res;
     }
     $res =  $this->transaction($request, function() use ($request, $id){
       $form = $this->create_form($request);
       $item = $this->model()->where('id', $id)->first();
       $item->change($form);
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function to_inform_page(Request $request , $id){
     $param = $this->get_param($request, $id);
     $param['fields'] = [
       'event_term' => [
         'label' => '開催期間',
         'size' => 6,
       ],
       'status_name' => [
         'label' => 'ステータス',
         'size' => 6,
       ],
       'title' => [
         'label' => '件名',
       ],
       'body' => [
         'label' => '内容'
       ],
     ];

     return view($this->domain.'.to_inform', [])
     ->with($param);
   }
   public function to_inform(Request $request , $id){
     set_time_limit(1200);
     $param = $this->get_param($request, $id);
     $select_send_user_ids = $request->get('select_send_user_ids');
     $send_users = EventUser::where('event_id', $id)->whereIn('id', $select_send_user_ids)->get();
     if(count($send_users)<1){
       $res = $this->error_response('送信対象がありません');
     }
     else {
       $res = $this->transaction($request, function() use ($send_users){
         foreach($send_users as $send_user){
           $send_user->to_inform();
         }
         return $this->api_response(200, '', '', $send_users);
       }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
     }
     return $this->save_redirect($res, $param, '送信しました');
   }
   public function schedule_lists(Request $request, $id){
     return "hoge";
   }
   public function schedule_lists_commit(Request $request, $id){
     return "fuga";
   }
}
