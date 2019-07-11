<?php

namespace App\Http\Controllers;
use App;
use App\User;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends UserController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function auth()
   {
     //abort('500', 'ページが存在しません');
     $user = Auth::user();
     if(isset($user)){
       //ログイン済みであれば自動ログイン
        return view('home');
     }
     return view('auth.login');
   }
   public function credential(Request $request, $id=0)
   {
     if(!$request->has('password')) return $this->bad_request();

     $user = null;
     $email = "";
     if($id>0) $user = User::where('id', $id)->first();
     if(isset($user)) $email = $user->email;
     else if($request->has('email')) $email = $request->get('email');

     $form = $request->all();
     if (Auth::attempt(['email' => $email, 'password' => $form['password']]))
     {
       $user = Auth::user();
       $access_key = $this->create_token();
       $res = $user->update(['access_key' => $access_key]);
       return $this->api_response(200, $access_key, "", $user);
     }

     return $this->forbidden();
   }
   /**
    * パスワード忘れた場合画面表示
    *
    * @return view
    */
   public function forget(){
     return view('auth.forget');
   }
   /**
    * パスワード忘れた場合メール送信
    * access_keyをリクエストパラメータ keyにて更新し、メールリンクにて送信
    * @param  \Illuminate\Http\Request  $request
    * @return view
    */
   public function reset_mail(Request $request){

     $email = trim($request->email);
     $_user = User::where('email',$email);
     if($_user->count() < 1){
       return back()->with(['error_message' => __('messages.error_email')]);
     }
     $user = $_user->first();
     $access_key = $this->create_token();
     $_user = $_user->update(['access_key' => $access_key]);
     $this->send_mail($email,
      __('labels.password_setting'),
      [
      'locale' => $locale,
      'user_name' => $user->details()["name"],
      'access_key' => $access_key
      ],
      'text',
      'password_reset',
      $_user->locale()
    );
     return back()->with(['success_message' =>  __('messages.info_password_setting_send')]);
   }
   /**
    * パスワード再設定画面
    * リクエストパラメータ keyが、access_keyと一致しない場合はアクセスできない
    * @param  \Illuminate\Http\Request  $request
    * @return view
    */
   public function password_setting(Request $request){
     $access_key = $request->get('key');
     if(!$this->is_enable_token($access_key)){
       abort(403);
     }
     $user = User::where('access_key',$access_key);
     if($user->count() < 1){
       abort(404);
     }
     $user = $user->first();
     return view('auth.reset',[
     'access_key' => $access_key
     ]);
   }
   /**
    * パスワード再設定処理
    *　更新成功時にログイン画面へ遷移
    * @param  \Illuminate\Http\Request  $request
    * @return redirect
    */
   public function password_settinged(Request $request){
     $access_key = $request->access_key;
     if(!$this->is_enable_token($access_key)){
       abort(403);
     }
     $user = User::where('access_key',$access_key)->first();
     if(!isset($user)){
       abort(403);
     }
     $form = $request->all();
     $res = $this->update_user_password($user->id, $form['password']);
     $message = __('info_password_setting_update');
     if($this->is_success_response($res)){
       if (Auth::attempt(['email' => $user->email, 'password' => $form['password']]))
       {
         return $this->save_redirect($res, [], $message, '/home?locale='.$request->get('locale'));
       }
     }
     return $this->save_redirect($res, [], $message);

   }
}
