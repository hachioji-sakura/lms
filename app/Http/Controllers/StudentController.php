<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\UserCalendar;
use App\Models\StudentRelation;
use App\Models\GeneralAttribute;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class StudentController extends UserController
{
  public $domain = "students";
  public $table = "students";
  public $domain_name = "生徒";
  /**
   * このdomainで管理するmodel
   *
   * @return model
   */
  public function model(){
    return Student::query();
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
   $_table = $this->search($request);
   return view($this->domain.'.tiles', $_table)
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
    $id = intval($id);
    $user = $this->login_details();
    if(empty($user)){
      //ログインしていない
      abort(419);
    }

    $ret = [
       'domain' => $this->domain,
       'domain_name' => $this->domain_name,
       'user' => $user,
       'mode'=>$request->mode,
       'search_word'=>$request->search_word,
       'attributes' => $this->attributes(),
    ];
    if(is_numeric($id) && $id > 0){
      $ret['item'] = $this->model()->where('id', $id)->first()->user->details();
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
    $items = $this->model()->with('user.image');

    $items = $this->_search_scope($request, $items);

   $items = $this->_search_pagenation($request, $items);

   $items = $this->_search_sort($request, $items);

   $items = $items->get();
   return ["items" => $items];
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
    $user = $this->login_details();

    //ID 検索
    if(isset($request->id)){
      $items = $items->where($this->table.'.id',$request->id);
    }
    if($this->is_parent($user->role)){
      //自分の子供のみ閲覧可能
      $items = $items->findChild($user->id);
    }
    else if($this->is_teacher($user->role)){
      //自分の担当生徒のみ閲覧可能
      $items = $items->findChargeStudent($user->id);
    }

    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }
    //ステータス
    if(isset($request->status)){
      $items = $items->findStatuses($request->status);
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
    if(!$this->is_parent($param['user']->role)){
      abort(403);
    }
    $param['student'] = null;

    return view($this->domain.'.create',
      ['error_message' => ''])
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
     if(!$this->is_parent($param['user']->role)){
       abort(403);
     }
     $form = $request->all();
     $parent = StudentParent::where('id', $param['user']->id)->first();
     $form['create_user_id'] = $param['user']->user_id;
     $parent->brother_add($form);
     $form['parent_name_first'] = $param['user']->name_first;
     $form['parent_name_last'] = $param['user']->name_last;
     $form['send_to'] = 'parent';
     $this->send_mail($param['user']->email, '生徒情報登録完了', $form, 'text', 'register');

     $param['success_message'] = '生徒情報登録完了しました。';
     return redirect('/home')
      ->with($param);

   }
   /**
    * データ更新時のパラメータチェック
    *
    * @return \Illuminate\Http\Response
    */
   public function save_validate(Request $request)
   {
     //保存時にパラメータをチェック
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
   $model = $this->model()->where('id',$id);
   if(!isset($model)){
      abort(404);
   }
   $model = $model->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   //コメントデータ取得
   $comments = $model->target_comments;
   if($this->is_teacher($user->role)){
     //講師の場合、公開されたコメントのみ閲覧可能
     $comments = $comments->where('publiced_at', '<=' , Date('Y-m-d'));
   }
   $comments = $comments->sortByDesc('created_at');

   //目標データ取得
   $milestones = $model->target_milestones;

   return view($this->domain.'.page', [
     'item' => $item,
     'comments'=>$comments,
     'milestones'=>$milestones,
     'use_icons'=>$this->get_image(),
   ])->with($param);
  }
  /**
   * 詳細画面表示
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
 public function calendar(Request $request, $id)
 {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   //目標データ取得
   $milestones = $model->target_milestones;

   $use_icons = DB::table('images')
     ->where('create_user_id','=',$user->user_id)
     ->orWhere('publiced_at','<=', date('Y-m-d'))
     ->get(['id', 'alias', 's3_url']);

   $view = "calendar";

   return view($this->domain.'.'.$view, [
     'item' => $item,
     'milestones'=>$milestones,
     'use_icons'=>$use_icons,
   ])->with($param);
 }
 public function schedule(Request $request, $id)
 {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   //目標データ取得
   $milestones = $model->target_milestones;

   $view = "schedule";
   $calendars = UserCalendar::findUser($item->user_id)->rangeDate(date('Y-m-d'))->get();
   foreach($calendars as $calendar){
     $calendar = $calendar->details();
   }
   $param["calendars"] = $calendars;
   return view($this->domain.'.'.$view, [
     'item' => $item,
     'milestones'=>$milestones,
     'use_icons'=> $this->get_image(),
   ])->with($param);
 }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $result = '';
    $param = $this->get_param($request, $id);
    $param['_edit'] = true;
    $param['student'] = $param['item'];
    /*
    if(!empty($param['user'])){
       if(!$this->is_parent($param['user']->role)){
         //親以外、ここからの生徒編集はできない
         abort(403);
       }
       $param['parent'] = $param['user'];
       $param['item'] = Student::where('id', $id)->first();
    }
    else {
      abort(403);
    }
    */
    return view($this->domain.'.edit',$param);

  }
  public function delete_page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $param['item']['name'] = $param['item']->name();
    $param['item']['kana'] = $param['item']->kana();
    $param['item']['birth_day'] = $param['item']->birth_day();
    $param['item']['gender'] = $param['item']->gender();
    $fields = [
      'name' => [
        'label' => '氏名',
      ],
      'kana' => [
        'label' => 'フリガナ',
      ],
      'birth_day' => [
        'label' => '生年月日',
      ],
      'gender' => [
        'label' => '性別',
      ],
    ];
    return view('components.page', [
      'action' => 'delete',
      '_page_origin' => $this->domain,
      'fields'=>$fields])
      ->with($param);
  }
  public function remind_page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $param['item']['name'] = $param['item']->name();
    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'name' => [
        'label' => '氏名',
      ],
    ];
    return view('components.page', [
      'action' => 'remind',
      '_page_origin' => $this->domain,
      'fields'=>$fields])
      ->with($param);
  }

  public function remind(Request $request, $id)
  {
    $result = '';
    $form = $request->all();
    $res = $this->api_response(200);
    $access_key = $this->create_token();
    $param = $this->get_param($request, $id);
    $result = '';
    if($param['item']->user->status===1){
      //token更新
      $param['item']->user->update( ['access_key' => $access_key]);
      $result = 'success';
    }
    else if($param['item']->user->status===0){
      //本登録済み
      $res = $this->error_response('このメールアドレスは本登録が完了しております。');
      $result = 'already';
    }
    if($this->is_success_response($res)){
      $this->send_mail($param['item']['email'],
        'ユーザー本登録のお願い（再送）', [
        'user_name' => $param['item']['name'],
        'access_key' => $access_key,
        'remind' => true,
        'send_to' => $param['item']->role,
      ], 'text', 'entry');
    }
    return $this->save_redirect($res, $param, '本登録依頼メールを送信しました');
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
    return $this->save_redirect($res, $param, $this->domain_name.'設定を更新しました');
  }

  public function _update(Request $request, $id)
  {
   $res = $this->save_validate($request);
   if(!$this->is_success_response($res)){
     return $res;
   }
   $form = $request->all();
   try {
     DB::beginTransaction();
     $user = $this->login_details();
     $form = $request->all();
     $form['create_user_id'] = $user->user_id;
     $item = $this->model()->where('id',$id)->first();
     $item = $item->profile_update($form);
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
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $param = $this->get_param($request, $id);

    $res = $this->_delete($request, $id);
    return $this->save_redirect($res, $param, $this->domain_name.'を削除しました');
  }

  public function _delete(Request $request, $id)
  {
   $form = $request->all();
   try {
     DB::beginTransaction();
     $item = $this->model()->where('id', $id)->first()->user->update(['status' => 9]);
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
}
