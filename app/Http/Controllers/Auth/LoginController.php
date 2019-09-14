<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\Teacher;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('guest')->except('logout');
    }
    protected function authenticated(Request $request, $user)
    {
      session()->regenerate();
      session()->put('login_role', null);
      session()->put('locale', $request->get('locale'));
      $user = Auth::user();
      $manager = Manager::where('user_id', $user->id)->first();
      $teacher = Teacher::where('user_id', $user->id)->first();
      if(isset($manager) && strpos(url()->previous(), 'managers/login')){
        //ログインモードを記録
        session()->put('login_role', "manager");
      }
      if(isset($manager) && !isset($teacher)){
        //事務権限しかない場合
        if($manager->is_admin()==true) session()->put('login_role', "manager");
        //session()->put('login_role', "manager");
      }

      // ログイン後のリダイレクト
      return redirect()->intended($this->redirectPath());
    }
    public function logout(Request $request){
      $is_manager = false;
      if(session('login_role') == 'manager'){
        $is_manager = true;
      }
      Auth::logout();
      session()->flush();
      session()->regenerate();
      if($request->has('back')){
        return back()->with([]);
      }
      if($is_manager){
        return redirect('managers/login');
      }
      return redirect($this->redirectTo);
    }
}
