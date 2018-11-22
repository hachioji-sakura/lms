<?php

namespace App\Http\Controllers;
use App\User;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
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
   public function mail_send(){
     Mail::raw('mail_send test',
      function($message) {
        $message->to('yasui.hideo@gmail.com')
          ->subject('tinker');
      });
   }
}
