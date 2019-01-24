<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;

use DB;

class StudentParentController extends StudentController
{
  public $domain = "parents";
  public $table = "parents";
  public $domain_name = "ご契約者様";
  /**
   * 体験授業申し込みページ
   *
   * @return \Illuminate\Http\Response
   */
  public function entry(Request $request)
  {
    $param = [];
    return view($this->domain.'.entry',
      ['sended' => ''])
      ->with($param);
   }
   /**
    * 体験授業申し込みページ
    *
    * @return \Illuminate\Http\Response
    */
   public function entry_store(Request $request)
   {
     $result = '';
     $form = $request->all();
     $res = $this->api_response(200);
     $access_key = $this->create_token();
     $request->merge([
       'access_key' => $access_key,
     ]);

     $user = User::where('email', $form['email'])->first();
     $result = '';
     if(!isset($user)){
       $res = $this->_entry_store($request);
       $result = 'success';
     }
     else {
       if($user->status===1){
         //すでにユーザーが仮登録されている場合は、tokenを更新
         $user->update( ['access_key' => $access_key]);
         $result = 'already';
       }
       else {
         //本登録済み
         $res = $this->error_response('このメールアドレスは本登録が完了しております。');
       }
     }
     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         'お申込み仮受付完了', [
         'user_name' => $form['name_last'].' '.$form['name_first'],
         'access_key' => $access_key,
       ], 'text', 'entry');
       return view($this->domain.'.entry',
         ['result' => $result]);
     }
     else {
       return $this->save_redirect($res, [], '', $this->domain.'/entry');
     }
   }
   public function _entry_store(Request $request)
   {
     $form = $request->all();
     try {
       DB::beginTransaction();
       $form["password"] = 'sakusaku';
       $items = StudentParent::entry($form);
       DB::commit();
       return $this->api_response(200, __FUNCTION__);
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
    * 本登録ページ
    *
    * @return \Illuminate\Http\Response
    */
   public function register(Request $request)
   {
     $result = '';
     $param = $this->get_param($request);
     if(!empty($param['user'])){
       $param['result'] = 'logout';
       return view($this->domain.'.register',$param);
     }
     else {
       $access_key = $request->get('key');
       if(!$this->is_enable_token($access_key)){
         $param['result'] = 'token_error';
         return view($this->domain.'.register',$param);
       }
       $user = User::where('access_key',$access_key);
       if($user->count() < 1){
         abort(404);
       }
       $param['parent'] = $user->first()->details();
       $param['access_key'] = $access_key;
     }
     return view($this->domain.'.register',$param);
    }
    /**
     * 本登録処理
     *
     * @return \Illuminate\Http\Response
     */
    public function register_update(Request $request)
    {
      $result = "success";
      $param = $this->get_param($request);
      $email = "";
      $password = "";
      $form = $request->all();
      if(!empty($param['user'])){
        //ログインユーザーがある場合は、操作させない
        abort(403);
      }
      $access_key = $request->access_key;
      if(!$this->is_enable_token($access_key)){
        $result = "token_error";
        return view($this->domain.'.register',
          ['result' => $result]
        );
      }
      $res = $this->_register_update($request);
      $email = $form['email'];
      $password = $form['password'];

      if($this->is_success_response($res)){
        if(empty($param['user'])){
          $this->send_mail($email, '生徒情報登録完了', $form, 'text', 'register');
          if (!Auth::attempt(['email' => $email, 'password' => $password]))
          {
            abort(500);
          }
        }
        return $this->save_redirect($res, $param, '生徒情報登録完了しました。', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key']);
      if($user->count() < 1){
        abort(403);
      }
      try {
        $user = $user->first();
        DB::beginTransaction();
        $parent = StudentParent::where('user_id', $user->id)->first();
        $parent->profile_update([
          'name_last' => $form['parent_name_last'],
          'name_first' => $form['parent_name_first'],
          'kana_last' => $form['parent_kana_last'],
          'kana_first' => $form['parent_kana_first'],
          'phone_no' => $form['phone_no'],
          'howto' => $form['howto'],
          'howto_word' => $form['howto_word'],
          'create_user_id' => $user->id,
        ]);
        $form['create_user_id'] = $user->id;
        $parent->brother_add($form);
        $user->set_password($form['password']);
        DB::commit();
        return $this->api_response(200, __FUNCTION__);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        //
        $items = StudentParent::all();
        return $items->toArray();
    }

}
