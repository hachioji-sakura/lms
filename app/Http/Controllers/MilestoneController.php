<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Milestone;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use DB;
class MilestoneController extends UserController
{
    public $domain = 'milestones';
    public $table = 'milestones';
    public $domain_name = '目標';
    /**
     * このdomainで管理するmodel
     *
     * @return model
     */
    public function model(){
      return Milestone::query();
    }
    public function get_target_user_id(Request $request){
      $user = $this->login_details($request);
      if($this->is_student($user->role)===true){
        return $user->user_id;
      }
      /*
      if($request->has('student_id')){
        $u = Student::where('id',$request->get('student_id'));
      }
      else if($request->has('teacher_id')){
        $u = Teacher::where('id',$request->get('teacher_id'));
      }
      else if($request->has('manager_id')){
        $u = Manager::where('id',$request->get('manager_id'));
      }
      */

      if($request->has('origin') && $request->has('item_id')){
        switch($request->get('origin')){
          case "students":
            $u = Student::where('id',$request->get('item_id'))->first();
            break;
          case "teachers":
            $u = Teacher::where('id',$request->get('item_id'))->first();
            break;
          case "managers":
            $u = Manager::where('id',$request->get('item_id'))->first();
            break;
        }
        if(isset($u)){
          return $u->user_id;
        }
      }
      return null;
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
      $form['type'] = $request->get('type');
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      $form['target_user_id'] = $this->get_target_user_id($request);
      return $form;
    }
    /**
     * 更新用フォーム
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function update_form(Request $request){
      $form = [];
      $form['type'] = $request->get('type');
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      return $form;
    }
    /**
     * 一覧表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @return view / domain.lists
     */
    public function index(Request $request)
    {
      $param = $this->get_param($request);
      $user = $param['user'];
      if(!$this->is_manager($user->role)){
        //事務以外 一覧表示は不可能
        abort(403);
      }
      $_table = $this->search($request);
      return view($this->domain.'.lists', $_table)
        ->with($param);
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      $ret = [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        'user' => $user,
        'origin' => $request->origin,
        'item_id' => $request->item_id,
        'teacher_id' => $request->teacher_id,
        'manager_id' => $request->manager_id,
        'student_id' => $request->student_id,
        'search_word'=>$request->search_word,
        'search_status'=>$request->status,
        'attributes' => $this->attributes(),
      ];
      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id','=',$id)->first();
        if($this->is_student($user->role) &&
          $item['create_user_id'] !== $user->user_id){
            //生徒は自分の起票したものしか編集できない
            abort(404);
        }
        $create_user = $item->create_user->details();
        $item->create_user_name = $create_user->name;
        unset($item->create_user);
        $item->_type_name = $item->type_name();

        $target_user = $item->target_user->details();
        $item->target_user_name = $target_user->name;
        unset($item->target_user);
        $ret['item'] = $item;
      }
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
      $items = $this->model();
      $user = $this->login_details($request);
      if($this->is_manager_or_teacher($user->role)!==true){
        $items = $items->mydata($user->user_id);
      }
      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);

      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $create_user = $item->create_user->details();
        $item->create_user_name = $create_user->name;
        unset($item->create_user);
        $target_user = $item->target_user->details();
        $item->target_user_name = $target_user->name;
        unset($item->target_user);
      }
      $fields = [
        'id' => [
          'label' => 'ID',
        ],
        'title' => [
          'label' => 'タイトル',
          'link' => 'show',
        ],
      ];
      if($this->is_manager_or_teacher($user->role)===true){
        //生徒以外の場合は、対象者も表示する
        $fields['target_user_name'] = [
          'label' => 'ユーザー',
        ];
      }

      $fields['created_at'] = [
        'label' => '登録日時',
      ];
      $fields['buttons'] = [
        'label' => '操作',
        'button' => ['edit', 'delete']
      ];
      return ['items' => $items->toArray(), 'fields' => $fields];
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
        $items = $items->where('status',$request->search_status);
      }
      //種別 検索
      if(isset($request->search_type)){
        $items = $items->where('type',$request->search_type);
      }
      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        $items = $items->where(function($items)use($search_words){
          foreach($search_words as $_search_word){
            if(empty($_search_word)) continue;
            $_like = '%'.$_search_word.'%';
            $items->orWhere('title','like',$_like)->orWhere('body','like',$_like);
          }
        });
      }

      return $items;
    }
    /**
     * 新規登録画面
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Request $request)
   {

      $param = $this->get_param($request);
      return view($this->domain.'.create',['_edit' => false])
        ->with($param);
    }

    /**
     * 新規登録
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $param = $this->get_param($request);

      $res = $this->_store($request);
      //生徒詳細からもCALLされる
      return $this->save_redirect($res, $param, $this->domain_name.'を登録しました');
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
      try {
        DB::beginTransaction();
        $item = $this->model()->create($form);
        DB::commit();
        return $this->api_response(200, '', '', $item);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
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
      if(empty($form['title']) || empty($form['body']) || empty($form['type'])){
        return $this->bad_request('リクエストエラー', '種別='.$form['type'].'/タイトル='.$form['title'].'/内容='.$form['body']);
      }
      return $this->api_response(200, '', '');
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
        'type' => [
          'label' => '種別',
        ],
        'title' => [
          'label' => 'タイトル',
        ],
        'body' => [
          'label' => '内容',
        ],
      ];
      if($this->is_manager_or_teacher($param['user']->role)===true){
        //生徒以外の場合は、対象者も表示する
        $fields['target_user_name'] = [
          'label' => 'ユーザー',
        ];
      }
      $fields['created_at'] = [
        'label' => '登録日時',
      ];
      $fields['updated_at'] = [
        'label' => '更新日時',
      ];

      return view('components.page', [
        'action' => $request->get('action'),
        'fields'=>$fields])
        ->with($param);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      return view($this->domain.'.create', [
        '_edit' => true])
        ->with($param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $res = $this->_update($request, $id);
      //生徒詳細からもCALLされる
      return $this->save_redirect($res, $param, $this->domain_name.'を更新しました');
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $res =  $this->transaction(function() use ($request, $id){
        $user = $this->login_details($request);
        $item = $this->model()->where('id',$id)->update($this->update_form($request));
        return $item;
      }, $this->domain_name.'更新しました。', __FILE__, __FUNCTION__, __LINE__ );
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
      //生徒詳細からもCALLされる
      return $this->save_redirect($res, $param, $this->domain_name.'を削除しました');
    }

    public function _delete(Request $request, $id)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details($request);
        $items = $this->model()->where('id',$id)->delete();
        DB::commit();
        return $this->api_response(200, '', '', $items);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
      }
    }
}
