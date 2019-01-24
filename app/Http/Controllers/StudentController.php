<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Student;
use App\Models\StudentParent;
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
    $ret = [
       'domain' => $this->domain,
       'domain_name' => $this->domain_name,
       'user' => $user,
       'mode'=>$request->mode,
       'search_word'=>$request->search_word,
       'attributes' => $this->attributes(),
    ];
    if(is_numeric($id) && $id > 0){
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
       ->join('users', 'users.id',$this->table.'.user_id')
       ->join('images', 'images.id','users.image_id');

   $items = $this->_search_scope($request, $items);

   $items = $this->_search_pagenation($request, $items);

   $items = $this->_search_sort($request, $items);

   $select_raw = <<<EOT
     $this->table.id,
     concat($this->table.name_last, '', $this->table.name_first) as name,
     concat($this->table.kana_last, '', $this->table.kana_first) as kana,
     images.s3_url as icon,
     $this->table.gender,
     $this->table.birth_day
EOT;
   $items = $items->selectRaw($select_raw)->get();
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
    $user = $this->login_details();
   //ID 検索
   if(isset($request->id)){
     $items = $items->where($this->table.'.id',$request->id);
   }
   //性別 検索
   if(isset($request->gender)){
     $items = $items->where($this->table.'.gender',$request->gender);
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
   if($this->is_parent($user->role)){
     //自分の子供のみ閲覧可能
     $items = $items->whereRaw('students.id in (select student_id from student_relations where student_parent_id=?)',[$user->id]);
   }
   else if($this->is_teacher($user->role)){
     if(!isset($request->filter) || $request->filter!=='all'){
       //filterにall指定がない
       $items = $items->whereRaw('students.id in (select student_id from charge_students where teacher_id=?)',[$user->id]);
     }
     //講師は削除された生徒は表示しない
     $items = $items->where('users.status', '!=', 9);
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
     $this->send_mail($param['user']->email, '生徒情報登録完了', $form, 'text', 'register');
     $param['success_message'] = '生徒情報登録完了しました。';
     return redirect('/home')
      ->with($param);

   }
   /**
    * 新規登録ロジック
    *
    * @return \Illuminate\Http\Response
    */
   public function _store(Request $request)
   {
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
   $model = $this->model()->find($id);
   if(!isset($model)){
      abort(404);
   }
   $model = $model->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];
   if(!isset($user)) {
     abort(403, 'sessionTimeout');
   }

   //コメントデータ取得
   $comments = $model->target_comments;
   if($this->is_teacher($user->role)){
     //講師の場合、公開されたコメントのみ閲覧可能
     $comments = $comments->where('publiced_at', '<=' , Date('Y-m-d'));
   }
   $comments = $comments->sortByDesc('created_at');

   //目標データ取得
   $milestones = $model->target_milestones;

   $use_icons = DB::table('images')
     ->where('create_user_id','=',$user->user_id)
     ->orWhere('publiced_at','<=', date('Y-m-d'))
     ->get(['id', 'alias', 's3_url']);
   return view($this->domain.'.page', [
     'item' => $item,
     'comments'=>$comments,
     'milestones'=>$milestones,
     'use_icons'=>$use_icons,
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
   $model = $this->model()->find($id)->user;
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
   if($param["mode"]==="list"){
     $view = "schedule";
     $request->merge([
       '_sort' => 'start_time',
     ]);
     $res = $this->call_api($request, url('/api_calendars/'.$item->user_id.'/'.date('Y-m-d')));
     if($this->is_success_response($res)){
       $param["calendars"] = $res["data"];
     }
   }

   return view($this->domain.'.'.$view, [
     'item' => $item,
     'milestones'=>$milestones,
     'use_icons'=>$use_icons,
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
    $param = $this->get_param($request);
    $param['_edit'] = true;
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
    return view($this->domain.'.edit',$param);

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
    return $this->save_redirect($res, $param, $this->domain_name.'を更新しました');
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
     $item = Student::find($id)->profile_update($form);
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
     $items = Student::find($id)->delete();
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
