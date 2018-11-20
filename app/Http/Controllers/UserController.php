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
  public $domain = "teachers";
  public $domain_name = "講師";
  protected $pagenation_line = 20;

  protected function user_create($form)
  {
    try {
      $user = User::create([
          'name' => $form['name'],
          'email' => $form['email'],
          'image_id' => $form['image_id'],
          'password' => Hash::make($form['password']),
      ]);
      return $this->api_responce(200, "", "", $user);
    }
    catch (\Illuminate\Database\QueryException $e) {
        return $this->error_responce("Query Exception", $e->getMessage());
    }
    catch(\Exception $e){
        return $this->error_responce("DB Exception", $e->getMessage());
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
  /**
   * メールアドレス存在チェック
   *
   * @return response
  */
  public function email_check($email){
    $item = User::where('email', $email)->first();
    if(isset($item)){
      $json = $this->api_responce(200,"","",["email"=>$email]);
      return $this->send_json_response($json);
    }
    return $this->send_json_response($this->notfound());
  }
  /**
   * 認証済みユーザーのデータを取得
   *
   * @return Collection User->attributes()
  */
  protected function login_attribute()
  {
    $user = Auth::user();
    if(!isset($user)){
      abort(403);
      return "";
    }
    return $user->attributes();
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
      return $this->api_responce(200, "", "");
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
    }
    catch(\Exception $e){
        echo $e->getMessage();
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
    }
  }
}
