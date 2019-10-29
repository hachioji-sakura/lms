<?php

namespace App\Http\Controllers;
use App;
use App\User;
use App\Models\Image;
use App\Models\Student;
use App\Models\GeneralAttribute;
use App\Models\Place;
use App\Models\PlaceFloor;
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

  protected $pagenation_line = 20;
  public function __construct()
  {
  }
  protected function attributes()
  {
    $attributes = [];
    $_attributes = GeneralAttribute::where('attribute_key', '!=', 'keys')
    ->orderBy('attribute_key', 'asc')
    ->orderBy('sort_no', 'asc')->get();
    foreach($_attributes as $_attribute){
      if(!isset($attributes[$_attribute['attribute_key']])){
        $attributes[$_attribute['attribute_key']] = [];
      }
      $attributes[$_attribute['attribute_key']][$_attribute['attribute_value']] = $_attribute['attribute_name'];
    }
    $places = Place::orderBy('sort_no', 'asc')->get();
    $attributes['places'] = $places;
    return $attributes;
  }
  protected function user_create($form)
  {

    if(!isset($form['status'])) $form['status']=0;
    if(!isset($form['access_key'])) $form['access_key']='';
    $res = $this->transaction(null, function() use ($form){
      $user = User::create([
          'name' => $form['name'],
          'email' => $form['email'],
          'image_id' => $form['image_id'],
          'status' => $form['status'],
          'access_key' => $form['access_key'],
          'password' => Hash::make($form['password']),
      ]);
      return $user;
    }, 'ユーザー登録', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
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
  protected function get_image($request)
  {
    $user = $this->login_details($request);
    return Image::findCreateUser($user->user_id)->publiced()->get();
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
    if($this->is_success_response($res)){
      $param['success_message'] = $success_message;
      if(!empty($redirect_url)){
        return redirect($redirect_url)
        ->with($param);
      }
    }
    else {
      $param['error_message'] = $res['message'];
      $param['error_message_description'] = $res['description'];
    }
    return back()->withInput()->with($param);
  }
  /**
   * メールアドレス存在チェック
   *
   * @return response
  */
  public function email_check(Request $request, $email){
    $user = $this->login_details($request);
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
    $user = $this->login_details($request);
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
    $user = $this->login_details($request);
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
  protected function login_details(Request $request)
  {
    $user = Auth::user();
    $api_token = $request->header('api-token');
    \Log::warning("login_details:".session('locale'));
    App::setLocale(session('locale'));
    if($request->has('locale')){
      App::setLocale($request->get('locale'));
    }
    if(!empty($api_token)){
      $user = User::where('access_key', $api_token)->first();
      if(isset($user))  Auth::loginUsingId($user->id);
    }
    if(!isset($user)){
      return null;
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
    * roleが事務の場合 true
    * @param string role
    * @return boolean
  */
  protected function is_staff($role)
  {
    if($role==="staff"){
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
  protected function get_pagedata($count, $line, $page)
  {
    $max_page = 0;
    $_list_start = 0;
    $_list_end = 0;
    if($count > 0){
      $max_page = floor(intval($count-1) / intval($line))+1;
      if($max_page < $page){
        $page = $max_page;
      }
      $_list_start = ($page-1)*$line;
      $_list_end = $_list_start+$line;
      if($count-$_list_start < $line){
        $_list_end = $count;
      }
      $_list_start++;
    }
    return ["_list_start" => $_list_start,
      "_list_end" => $_list_end,
      "_list_count" => $count,
      "_page" => $page,
      "_maxpage" => $max_page];
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
  public function update_user_image($user_id, $image_id)
  {
    if(!is_numeric($user_id) || !is_numeric($image_id)){
      return $this->bad_request("", "user_id($user_id),image_id($image_id)");
    }
    return $this->transaction(null, function() use ($user_id, $image_id){
      $user = User::where('id', $user_id)->first();
      $user->update(['image_id' => $image_id]);
      return $user;
    }, 'アイコン変更', __FILE__, __FUNCTION__, __LINE__ );
  }
  /**
   * パスワード更新
   *
   * @return resonse
  */
  public function update_user_password($user_id, $password)
  {
    if(!is_numeric($user_id) || empty($password)){
      return $this->bad_request("", "user_id($user_id)");
    }
    return $this->transaction($request, function() use ($user_id, $password){
      $user = User::where('id', $user_id)->first();
      $user->set_password($password);
      return $user;
    }, 'パスワード設定', __FILE__, __FUNCTION__, __LINE__ );
  }
  public function transaction($request, $callback, $logic_name, $__file, $__function, $__line){
      try {
        DB::beginTransaction();
        $result = $callback();
        DB::commit();
        // 二重送信対策
        if($request!=null){
          $request->session()->regenerateToken();
        }
        return $this->api_response(200, '', '', $result);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          $this->send_slack($logic_name.'エラー:'.$e->getMessage(), 'error', $logic_name);
          return $this->error_response('Query Exception', '['.$__file.']['.$__function.'['.$__line.']'.'['.$e->getMessage().']');
      }
      catch(\Exception $e){
          DB::rollBack();
          $this->send_slack($logic_name.'エラー:'.$e->getMessage(), 'error', $logic_name);
          return $this->error_response('DB Exception', '['.$__file.']['.$__function.'['.$__line.']'.'['.$e->getMessage().']');
      }
  }
}
