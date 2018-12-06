<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Manager;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
/*
*/
use DB;
class TeacherController extends StudentController
{
  public $domain = "teachers";
  public $table = "teachers";
  public $domain_name = "講師";
  public $default_image_id = 3;
  /**
   * このdomainで管理するmodel
   *
   * @return model
   */
  public function model(){
   return Teacher::query();
  }
  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request)
  {
   $_param = $this->get_param($request);
   $_table = $this->search($request);
   return view($this->domain.'.tiles', $_table)
    ->with($_param);
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
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'search_word'=>$request->search_word
    ];
    //講師・事務以外はNG
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if($this->is_teacher($user->role) && $id!==$user->id){
        //講師は自分のidしか閲覧できない
        abort(404);
      }
    }
    else {
      //id指定がない、かつ、事務以外はNG
      if($this->is_manager($user->role)!==true){
        abort(403);
      }
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
   $items = DB::table($this->table)
    ->join('users', 'users.id', '=', $this->table.'.user_id')
    ->join('images', 'images.id', '=', 'users.image_id');

   $items = $this->_search_scope($request, $items);

   $items = $this->_search_pagenation($request, $items);

   $items = $this->_search_sort($request, $items);

   $select_row = <<<EOT
    $this->table.id,
    $this->table.name,
    $this->table.kana,
    images.s3_url as icon
EOT;
   $items = $items->selectRaw($select_row)->get();
   return ["items" => $items->toArray()];
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
    $items = $items->where($this->table.'.id','=', $request->id);
   }
   //検索ワード
   if(isset($request->search_word)){
    $search_words = explode(' ', $request->search_word);
    foreach($search_words as $_search_word){
      $_like = '%'.$_search_word.'%';
      $items = $items->where($this->table.'.name','like', $_like)
       ->orWhere($this->table.'.kana','like', $_like);
    }
   }
   //メールアドレス検索
   if(isset($request->email)){
    $_like = '%'.$request->email.'%';
    $items = $items->where('users.email','like', $_like);
   }
   return $items;
  }
  public function _store(Request $request)
  {
   $form = $request->all();
   try {
    DB::beginTransaction();
    $form["image_id"] = $this->default_image_id;
    $res = $this->user_create($form);
    if($this->is_success_responce($res)){
      $form['user_id'] = $res["data"]->id;
      $user = $this->login_details();
      $form["create_user_id"] = $user->user_id;
      unset($form['image_id']);
      unset($form['_token']);
      unset($form['password']);
      unset($form['email']);
      unset($form['password-confirm']);
      $_item = $this->model()->create($form);
      DB::commit();
      return $this->api_responce(200, "", "", $_item);
    }
    return $res;
   }
   catch (\Illuminate\Database\QueryException $e) {
      DB::rollBack();
      return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
   }
   catch(\Exception $e){
      DB::rollBack();
      return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
   }
  }
  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show(Request $request, $id)
  {
    $_param = $this->get_param($request, $id);
    $model = $this->model()->find($id)->user;
    $item = $model->details();
    $user = $_param['user'];
    //コメントデータ取得
    $comments = $model->target_comments;
    $comments = $comments->sortByDesc('created_at');

    //目標データ取得
    $milestones = $model->target_milestones;

    foreach($comments as $comment){
      $create_user = $comment->create_user->details();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_icon = $create_user->icon;
    }
    $use_icons = DB::table('images')
      ->where('create_user_id','=',$user->user_id)
      ->orWhere('publiced_at','<=', date('Y-m-d'))
      ->get(['id', 'alias', 's3_url']);
    return view($this->domain.'.page', [
      'item' => $item,
      'comments'=>$comments,
      'milestones'=>$milestones,
      'use_icons'=>$use_icons,
    ])->with($_param);
  }
}
