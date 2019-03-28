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
      if(isset($user)){
        if($user->status==0){
          //ログイン済みであれば自動ログイン
          $user = $user->details();
          switch($user->role){
            case "manager" :
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
        abort(403);
      }
      else {
        return redirect('/login');
      }
    }
}
