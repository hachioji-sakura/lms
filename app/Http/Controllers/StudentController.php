<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Student;
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
  public function model(){
    return Student::query();
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
   $_param = $this->get_param($request);
   $_table = $this->search($request);
   return view($this->domain.'.tiles', $_table)
     ->with($_param);
  }
  public function get_param(Request $request, $id=null){
   $user = $this->login_details();
   $ret = [
     'domain' => $this->domain,
     'domain_name' => $this->domain_name,
     'user' => $user,
     'search_word'=>$request->search_word
   ];
   if(is_numeric($id) && $id > 0){
     //id指定がある
     if($this->is_student($user->role) && $id!==$user->id){
      //生徒は自分のidしか閲覧できない
      abort(404);
     }
   }
   else {
     //id指定がない、かつ、講師・事務以外はNG
     if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
     }
   }
   return $ret;
  }

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
     concat($this->table.name_last, '', $this->table.name_first) as name,
     concat($this->table.kana_last, '', $this->table.kana_first) as kana,
     images.s3_url as icon,
     $this->table.gender,
     $this->table.birth_day
EOT;
   $items = $items->selectRaw($select_row)->get();
   return ["items" => $items->toArray()];
  }
  public function _search_scope(Request $request, $items)
  {
   //ID 検索
   if(isset($request->id)){
     $items = $items->where($this->table.'.id','=', $request->id);
   }
   //性別 検索
   if(isset($request->gender)){
     $items = $items->where($this->table.'.gender','=', $request->gender);
   }
   //検索ワード
   if(isset($request->search_word)){
     $search_words = explode(' ', $request->search_word);
     foreach($search_words as $_search_word){
      $_like = '%'.$_search_word.'%';
      $items = $items->where($this->table.'.name_last','like', $_like)
        ->orWhere($this->table.'.name_first','like', $_like)
        ->orWhere($this->table.'.kana_last','like', $_like)
        ->orWhere($this->table.'.kana_first','like', $_like);
     }
   }

   //メールアドレス検索
   if(isset($request->email)){
     $_like = '%'.$request->email.'%';
     $items = $items->where('users.email','like', $_like);
   }

   return $items;
  }
  /**
   * Show the form for creating a new resource.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $_param = $this->get_param($request);
    return view($this->domain.'.create',
      ['error_message' => ''])
      ->with($_param);
   }
   /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
   public function store(Request $request)
   {
    $_param = $this->get_param($request);

    $res = $this->_store($request);
    //生徒詳細からもCALLされる
    return $this->save_redirect($res, $_param, $this->domain_name.'を登録しました', str_replace('_', '/', $request->get('_page_origin')));
   }
   public function _store(Request $request)
   {
     $form = $request->all();
     try {
      DB::beginTransaction();
      $form["name"] = $form['name_last'].' '.$form['name_first'];
      //デフォルトのアイコンは性別と一緒にしておく
      $form["image_id"] = $form['gender'];
      $res = $this->user_create($form);
      if($this->is_success_responce($res)){
        $form['user_id'] = $res["data"]->id;
        $user = $this->login_details();
        $form["create_user_id"] = $user->user_id;
        unset($form['name']);
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
   public function save_validate(Request $request)
   {
     //保存時にパラメータをチェック
     return $this->api_responce(200, '', '');
   }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
   $_param = $this->get_param($request);

   $student = Student::find($id)->user;
   $item = $student->details();
   $user = $_param['user'];
   //コメントデータ取得
   $comments = $student->target_comments;
   if($this->is_teacher($user->role)){
     //講師の場合、公開されたコメントのみ閲覧可能
     $comments = $comments->where('publiced_at', '<=' , Date('Y-m-d'));
   }
   $comments = $comments->sortByDesc('created_at');

   //目標データ取得
   $milestones = $student->target_milestones;

   foreach($comments as $comment){
     $create_user = $comment->create_user->details();
     $comment->create_user_name = $create_user->name;
     $comment->create_user_icon = $create_user->icon;
     unset($comment->create_user);
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
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $_param = $this->get_param($request, $id);
    return view($this->domain.'.create', [
      '_edit' => true])
      ->with($_param);
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
    $_param = $this->get_param($request, $id);
    $res = $this->_update($request, $id);
    return $this->save_redirect($res, $_param, $this->domain_name.'を更新しました');
  }

  public function _update(Request $request, $id)
  {
   $res = $this->save_validate($request);
   if(!$this->is_success_responce($res)){
     return $res;
   }
   $form = $request->all();
   try {
     DB::beginTransaction();
     $user = $this->login_details();
     $form = $request->all();
     $item = Student::find($id)->update($form);
     DB::commit();
     return $this->api_responce(200, '', '', $item);
   }
   catch (\Illuminate\Database\QueryException $e) {
      DB::rollBack();
      return $this->error_responce('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
   }
   catch(\Exception $e){
      DB::rollBack();
      return $this->error_responce('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
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
    $_param = $this->get_param($request, $id);

    $res = $this->_delete($request, $id);
    return $this->save_redirect($res, $_param, $this->domain_name.'を削除しました');
  }

  public function _delete(Request $request, $id)
  {
   $form = $request->all();
   try {
     DB::beginTransaction();
     $items = Student::find($id)->delete();
     DB::commit();
     return $this->api_responce(200, '', '', $items);
   }
   catch (\Illuminate\Database\QueryException $e) {
      DB::rollBack();
      return $this->error_responce('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
   }
   catch(\Exception $e){
      DB::rollBack();
      return $this->error_responce('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
   }
  }
}
