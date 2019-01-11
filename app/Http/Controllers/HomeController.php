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
    public function index()
    {
      $user = Auth::user()->details();
      if(isset($user)){
        if($user->status===1){
        }
        else {
          //ログイン済みであれば自動ログイン
          switch($user->role){
            case "manager" :
              return redirect('/managers/'.$user->id);
              break;
            case "teacher" :
              return redirect('/teachers/'.$user->id);
              break;
            case "parent" :
              return redirect('/students');
              break;
            case "student" :
              return redirect('/students/'.$user->id);
              break;
          }
        }
      }
      else {
        return redirect('/login');
      }
    }
}
