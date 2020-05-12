<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentParent;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = Auth::user();
      \Log::warning("HomeController:index");
      \Log::warning("user_id=".$user->id);

      if(isset($user)){
        if($request->has('locale')){
          session()->put('locale', $request->get('locale'));
        }
        if($user->status==0){
          //ログイン済みであれば自動ログイン
          $user = $user->details();
          switch($user->role){
            case "manager" :
              return redirect('/managers/'.$user->id);
              break;
            case "staff" :
              return redirect('/managers/'.$user->id);
              break;
            case "teacher" :
              return redirect('/teachers/'.$user->id);
              break;
            case "parent" :
            /*
              if(count($user->relations)===1){
                //子供が一人の場合は、その子の詳細を表示
                $relation = $user->relations->first();
                return redirect('/students/'.$relation->student_id);
              }
            */
              return redirect('/parents/'.$user->id);
              break;
            case "student" :
              return redirect('/students/'.$user->id);
              break;
          }
        }
        Auth::logout();
        return redirect('/login?status='.$user->status);
      }
      else {
        return redirect('/login');
      }
    }
    public function unsubscribe(Request $request){
      return $this->_role_redirect($request, 'unsubscribe');
    }
    public function recess(Request $request){
      return $this->_role_redirect($request, 'recess');
    }
    public function late_arrival(Request $request){
      return $this->_role_redirect($request, 'late_arrival');
    }
    public function _role_redirect(Request $request, $view){
      $user = Auth::user();
      if(isset($user)){
        if($request->has('locale')){
          session()->put('locale', $request->get('locale'));
        }
        //ログイン済みであれば自動ログイン
        $user = $user->details();
        switch($user->role){
          /*
          case "manager" :
            return redirect('/managers/'.$user->id.'/'.$view);
            break;
          case "teacher" :
            return redirect('/teachers/'.$user->id.'/'.$view);
            break;
          */
          case "parent" :
              $students = $user->get_enable_students();
              if(count($students)==1){
                return redirect('/students/'.$students[0]->id.'/'.$view);
              }
              else if($request->has('student_id')){
                return redirect('/students/'.$request->get('student_id').'/'.$view);
              }
              else {
                return redirect('/parents/'.$user->id.'/'.$view);
              }
            break;
          case "student" :
            return redirect('/students/'.$user->id.'/'.$view);
            break;
        }
      }
      abort(403);
    }
}
