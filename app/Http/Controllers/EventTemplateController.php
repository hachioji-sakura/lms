<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventTemplate;
use App\Models\EventTemplateTag;

//class EventTemplateController extends Controller
class EventTemplateController extends MilestoneController
{
  public $domain = 'event_templates'; //URLで使われるページ名
  public $table = 'event_templates'; //スキーマ名(lms.)無しのテーブル名

  public function model(){
    return EventTemplate::query();
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
      'name' => [
        'label' => 'イベント名称'
      ],
      'lesson' => [
        'label' => '担当部門'
      ],
      'grade' => [
        'label' => '学年'
      ],
      'remark' => [
        'label' => '説明'
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

  /**
   * 新規登録用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['create_user_id'] = $user->user_id;
    $form['role'] = $request->get('role');
    $form['grade'] = $request->get('grade');
    $form['lesson'] = $request->get('lesson');
    $form['name'] = $request->get('name');
    $form['remark'] = htmlentities($request->get('remark'), ENT_QUOTES, 'UTF-8');
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
    if(empty($form['name']) || empty($form['role']) || empty($form['lesson'])){
      return $this->bad_request('リクエストエラー', '種別='.$form['name']);
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
      $item = EventTemplate::add($form);
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }
   public function _update(Request $request, $id)
   {
     $res = $this->save_validate($request);
     if(!$this->is_success_response($res)){
       return $res;
     }
     $res =  $this->transaction($request, function() use ($request, $id){
       $form = $this->update_form($request);
       $item = $this->model()->where('id', $id)->first();
       $item->change($form);
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
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

}
