<?php

namespace App\Http\Controllers;
use App\Models\ActionLog;
use Illuminate\Http\Request;

class ActionLogController extends MailLogController
{
  public $domain = "actionlogs";
  public $table = "action_logs";
  public function model(){
    return ActionLog::query();
  }
  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request)
  {
    $items = $this->model();
    $user = $this->login_details($request);
    if($this->is_manager_or_teacher($user->role)!==true){
      //生徒の場合は自分自身を対象とする
      $items = $items->mydata($user->user_id);
    }
    $items = $this->_search_scope($request, $items);
    $count = $items->count();
    $items = $this->_search_pagenation($request, $items);

    $request->merge([
      '_sort_order' => 'desc',
      '_sort' => 'created_at',
    ]);
    if($request->has('is_asc') && $request->get('is_asc')==1){
      $request->merge([
        '_sort_order' => 'asc',
      ]);
    }

    $items = $this->_search_sort($request, $items);
    $items = $items->get();
    foreach($items as $item){
      $item = $item->details();
    }
    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      "url" => [
        "label" => "url",
        "link" => "show",
      ],
      "method" => [
        "label" => "method",
      ],
      "session_id" => [
        "label" => "session_id",
      ],
      "login_user_id" => [
        "label" => "login_user_id",
      ],
      "client_ip" => [
        "label" => "client_ip",
      ],
      "created_date" => [
        "label" => __('labels.add_datetime'),
      ],
    ];

    return ['items' => $items, 'fields' => $fields, 'count' => $count];
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
    //種別 検索
    if(isset($request->search_type)){
      //$items = $items->findTemplates($request->search_type);
    }
    //検索ワード
    if(isset($request->search_word)){
      //$items = $items->searchWord($request->search_word);
    }

    return $items;
  }
  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    $form = $request->all();
    return $this->api_response(200, '', '');
  }

  public function update_form(Request $request){
    $form = [];
    return $form;
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

    $fields = [
      'server_name' => [
        'label' => 'server_name',
        'size' => 6
      ],
      'server_ip' => [
        'label' => 'server_ip',
        'size' => 6
      ],
      'session_id' => [
        'label' => 'session_id',
        'size' => 6
      ],
      'client_ip' => [
        'label' => 'client_ip',
        'size' => 6
      ],
      'user_agent' => [
        'label' => 'user_agent',
        'size' => 6
      ],
      'language' => [
        'label' => 'language',
        'size' => 6
      ],
      'url' => [
        'label' => 'url',
        'size' => 8
      ],
      'method' => [
        'label' => 'method',
        'size' => 4
      ],
      'referer' => [
        'label' => 'referer',
        'size' => 8
      ],
      'login_user_id' => [
        'label' => 'login_user_id',
        'size' => 4
      ],
      'post_param' => [
        'label' => 'post_param',
        'size' => 12
      ],
    ];
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
      'size' => 6
    ];
    $fields['updated_date'] = [
      'label' => __('labels.upd_datetime'),
      'size' => 6
    ];

    return view('components.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
      ->with($param);
  }
}
