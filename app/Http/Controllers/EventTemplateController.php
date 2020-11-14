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
  public function show_fields($type=''){
    $ret = [
      'role' => [
        'label' => '送信対象'
      ],
      'name' => [
        'label' => '件名',
      ],
      'lesson' => [
        'label' => '担当部門'
      ],
      'grade' => [
        'label' => '学年'
      ],
      'remark' => [
        'label' => '内容'
      ],
      'create_user_name' => [
        'label' => '作成ユーザID'
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
      'role' => [
        'label' => '送信対象'
      ],
      'name' => [
        'label' => '件名',
        'link' => 'show',
      ],
      'lesson' => [
        'label' => '担当部門'
      ],
      'grade' => [
        'label' => '学年'
      ],
      'create_user_name' => [
        'label' => '作成ユーザー'
      ],
   ];
   $fields['buttons'] = [
     'label' => '操作',
     'button' => ['edit', 'delete']
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
    $form['user_role'] = $request->get('user_role');
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
    if(empty($form['name']) || empty($form['user_role']) || empty($form['lesson'])){
      return $this->bad_request('リクエストエラー', ''.$form['name']);
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
