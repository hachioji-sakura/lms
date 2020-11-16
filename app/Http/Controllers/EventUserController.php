<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUser;

class EventUserController extends EventController
{
  public $domain = 'event_users';
  public $table = 'event_users';

  public function model(){
    return EventUser::query();
  }
  public function get_param(Request $request, $id=null){
    $ret = parent::get_param($request, $id);
    if($id==null && !$request->has('event_id')) abort(404);

    $ret['event_id'] = $request->get('event_id');
    return $ret;
  }
  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request)
  {
    if(!$request->has('event_id')) abort(404);

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
    $user = $param['user'];
    if(!$this->is_manager($user->role)){
      //事務以外 一覧表示は不可能
      abort(403);
    }
    $_table = $this->search($request);
    $param['event_id'] = $request->get('event_id');
    return view($this->domain.'.lists', $_table)
      ->with($param);
  }
  /**
   * イベント画面表示
   *
   */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $fields = [
      'id' => [
        'label' => 'ID'
      ],
      'user_name' => [
        'label' => '対象者'
      ],
      'status_name' => [
        'label' => 'ステータス'
      ],
      'created_date' => [
        'label' => '登録日'
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
    $items = $items->paginate();

    $fields = [
      'user_name' => [
        'label' => '対象者',
        "target" => '_blank',
        "link" => function($row){
          return $row->url;
        }
      ],
      'user_role' => [
        'label' => '権限'
      ],
      'status_name' => [
        'label' => 'ステータス'
      ],
      'tags' => [
        'label' => 'タグ'
      ],
      'sended_date' => [
        'label' => '送信日'
      ],
      'created_date' => [
        'label' => '登録日'
      ],
   ];
   $fields['buttons'] = [
     'label' => '操作',
     'button' => ['delete']
   ];
    return ['items' => $items, 'fields' => $fields];
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
    //event_id 検索
    if(isset($request->event_id)){
      $items = $items->where('event_id',$request->event_id);
    }
    //status 検索
    if(isset($request->search_status)){
      $items = $items->where('status',$request->search_status);
    }


    return $items;
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
    $form['event_id'] = $request->get('event_id');
    $form['user_id'] = $request->get('user_id');
    return $form;
  }
  /**
   * 更新用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function update_form(Request $request){
    return $this->create_form($request);
  }
  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    $form = $request->all();
    //保存時にパラメータをチェック
    if(empty($form['user_id']) || empty($form['event_id'])){
      return $this->bad_request('リクエストエラー', ''.$form['event_id']);
    }
    return $this->api_response(200, '', '');
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
      foreach($form['user_id'] as $user_id){
        $item = EventUser::create([
          'event_id' => $form['event_id'],
          'user_id' => $user_id
        ]);
      }
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, $id)
   {
     $param = $this->get_param($request, $id);
     $res = $this->_delete($request, $id);
     if($request->has('api')){
       return $this->api_response(200, '削除しました。', '');
     }
     return $this->save_redirect($res, $param, '削除しました。');
   }

   public function _delete(Request $request, $id)
   {
     $form = $request->all();
     $res = $this->transaction($request, function() use ($request, $form, $id){
       $item = $this->model()->where('id', $id)->first();
       $item->dispose();
       return $this->api_response(200, '', '', $item);
     }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   /**
    * 新規登録画面
    *
    * @return \Illuminate\Http\Response
    */
  public function create(Request $request)
  {

     $param = $this->get_param($request);
     $event = Event::where('id', $param['event_id'])->first();
     $param['event'] = $event;
     return view($this->domain.'.create',['_edit' => false])
       ->with($param);
   }
}
