<?php

namespace App\Http\Controllers;
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
       return back()->with(['error_message' => 'メールアドレスが間違っています']);
     }
     $user = $_user->first()->details();
     $access_key = $this->create_token();
     $_user = $_user->update(['access_key' => $access_key]);
     $this->send_mail($email,
      'パスワード再設定',
      [
      'user_name' => $user->name,
      'access_key' => $access_key
      ],
      'text',
      'password_reset');
     return back()->with(['success_message' => 'パスワード再設定メールを送信しました']);
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
     $user = $user->first()->details();
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

     $user = User::where('access_key',$access_key);
     if($user->count() < 1){
       abort(403);
     }
     $user = $user->first()->details();
     $form = $request->all();
     $res = $this->update_user_password($user->user_id, $form['password']);
     if (Auth::attempt(['email' => $user->email, 'password' => $form['password']]))
     {
       return $this->save_redirect($res, [], 'パスワード更新しました。', '/home');
     }
     abort(500);
   }
}
