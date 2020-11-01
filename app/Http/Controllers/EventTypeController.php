<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventType;

//class EventTypeController extends Controller
class EventTypeController extends MilestoneController
{
  public $domain = 'event_types'; //URLで使われるページ名
  public $table = 'event_types'; //スキーマ名(lms.)無しのテーブル名

  public function model(){
    return EventType::query();
  }

  /**
   * イベント画面表示
   *
   */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $fields = [
      'role' => [
        'label' => '送信対象'
      ],
      'event_name' => [
        'label' => 'イベント名称'
      ],
      'lesson' => [
        'label' => '担当部門'
      ],
      'grade' => [
        'label' => '学年'
      ],
      'body' => [
        'label' => '備考'
      ],
      'create_user_id' => [
        'label' => '作成ユーザID'
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
      'role' => [
        'label' => '送信対象'
      ],
      'event_name' => [
        'label' => 'イベント名称'
      ],
      'lesson' => [
        'label' => '担当部門'
      ],
      'grade' => [
        'label' => '学年'
      ],
      'body' => [
        'label' => '備考'
      ],
      'create_user_id' => [
        'label' => '作成ユーザID'
      ],
   ];

    return ['items' => $items, 'fields' => $fields];

  }


}
