<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Image;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class UserController extends Controller
{
  public $domain = "users";
  public $domain_name = "ユーザー";
  protected $pagenation_line = 20;
  protected function user_create($form)
  {
    try {
      if(!isset($form['status'])) $form['status']=0;
      $user = User::create([
          'name' => $form['name'],
          'email' => $form['email'],
          'image_id' => $form['image_id'],
          'status' => $form['status'],
          'password' => Hash::make($form['password']),
      ]);
      return $this->api_response(200, "", "", $user);
    }
    catch (\Illuminate\Database\QueryException $e) {
        return $this->error_response("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
    catch(\Exception $e){
        return $this->error_response("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
  }
  protected function _search_pagenation(Request $request, $items)
  {
    $_line = $this->pagenation_line;
    if(isset($request->_line)){
      $_line = $request->_line;
    }
    if(isset($request->_page)){
      $_offset = ($request->_page-1)*$_line;
      if($_offset < 0) $_offset = 0;
      $items = $items->offset($_offset);
      $items = $items->limit($_line);
    }

    return $items;
  }
  protected function _search_sort(Request $request, $items)
  {
    if(isset($request->_sort)){
      $_sort_order = "asc";
      if(isset($request->_sort_order) && $request->_sort_order==="desc") {
        $_sort_order = "desc";
      }
      $items = $items->orderBy($request->_sort, $_sort_order);
    }
    return $items;
  }
  protected function save_redirect($res, $param, $success_message, $redirect_url=''){
   if(empty($redirect_url)) $redirect_url ='/'.$this->domain;
   if($this->is_success_response($res)){
     $param['success_message'] = $success_message;
     return redirect($redirect_url)
      ->with($param);
   }
   else {
     $param['error_message'] = $res['message'];
     $param['error_message_description'] = $res['description'];
     return back()->with($param);
   }
  }

  /**
   * メールアドレス存在チェック
   *
   * @return response
  */
  public function email_check($email){
    $user = $this->login_details();
    if(!isset($user) || !is_numeric($user->user_id)){
      abort(403);
    }
    $item = User::where('email', $email)->first();
    if(isset($item)){
      $json = $this->api_response(200,"","",["email"=>$email]);
      return $this->send_json_response($json);
    }
    return $this->send_json_response($this->notfound());
  }
  /**
   * パスワード設定画面
   *
   * @return response
  */
  public function password(Request $request){
    $user = $this->login_details();
    if(!isset($user) || !is_numeric($user->user_id)){
      abort(403);
    }
    return view('dashboard.password', ['user' => $user])->with(["search_word"=>$request->search_word]);
  }
  /**
   * パスワード更新
   *
   * @return response
  */
  public function password_update(Request $request){
    $user = $this->login_details();
    if(!isset($user) || !is_numeric($user->user_id)){
      abort(403);
    }
    $form = $request->all();
    $res = $this->update_user_password($user->user_id, $form['password']);
    if($this->is_success_response($res)){
      return back()->with([
        'success_message' => 'パスワード更新しました。'
      ]);
    }
    else {
      return back()->with([
        'error_message' => $res["message"],
        'error_message_description' => $res["description"]
      ]);
    }
  }

  /**
   * 認証済みユーザーのデータを取得
   *
   * @return Collection User->details()
  */
  protected function login_details()
  {
    $user = Auth::user();
    if(!isset($user)){
      abort(403);
      return "";
    }
    return $user->details();
  }

  /**
    * roleが事務の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_manager($role)
  {
    if($role==="manager"){
      return true;
    }
    return false;
  }
  /**
    * roleが講師の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_teacher($role)
  {
    if($role==="teacher"){
      return true;
    }
    return false;
  }
  /**
    * roleが生徒の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_student($role)
  {
    if($role==="student"){
      return true;
    }
    return false;
  }
  /**
    * roleが生徒または保護者の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_student_or_parent($role)
  {
    if($role==="student" || $role==="parent"){
      return true;
    }
    return false;
  }
  /**
    * roleが保護者の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_parent($role)
  {
    if($role==="parent"){
      return true;
    }
    return false;
  }
  /**
    * roleが事務、もしくは講師の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_manager_or_teacher($role)
  {
    if($role==="manager" || $role==="teacher"){
      return true;
    }
    return false;
  }
  /**
   * アイコン更新
   *
   * @return resonse
  */
  protected function update_user_image($user_id, $image_id)
  {
    if(!is_numeric($user_id) || !is_numeric($image_id)){
      return $this->bad_request("", "user_id($user_id),image_id($image_id)");
    }
    try {
      DB::beginTransaction();
      User::where('id', $user_id)->update(['image_id' => $image_id]);
      DB::commit();
      return $this->api_response(200, "", "");
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_response("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
    catch(\Exception $e){
        echo $e->getMessage();
        DB::rollBack();
        return $this->error_response("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
  }
  /**
   * パスワード更新
   *
   * @return resonse
  */
  protected function update_user_password($user_id, $password)
  {
    if(!is_numeric($user_id) || empty($password)){
      return $this->bad_request("", "user_id($user_id),image_id($image_id)");
    }
    try {
      DB::beginTransaction();
      User::where('id', $user_id)->update(['password' => Hash::make($password)]);
      DB::commit();
      return $this->api_response(200, "", "");
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_response("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
    catch(\Exception $e){
        echo $e->getMessage();
        DB::rollBack();
        return $this->error_response("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
  }

}
