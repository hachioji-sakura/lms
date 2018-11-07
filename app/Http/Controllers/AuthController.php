<?php

namespace App\Http\Controllers;

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
       $user = Auth::user();
       if(isset($user)){
         echo "true";
       }
       else {
         echo "false";
       }
       //$user->id より、各ユーザーを引き当てる
       return view('rest.create');
   }
   public function login()
   {
       $user = Auth::user();
       //$user->id より、各ユーザーを引き当てる
       if(isset($user)){
         //login成功
         return view('rest.create');
       }
       return view('rest.create');
   }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      return view('rest.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      $restdata = new Restdata;
      $form = $request->all();
      unset($form['_token']);
      $restdata->fill($form)->save();
      return redirect('/rest');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      $items = Restdata::find($id);
      return $items->toArray();
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $target = Restdata::find($id);
    return view('rest.create', ['form' => $target]);

  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      $items = Restdata::find($id);
      $form = $request->all();
      unset($form['_token']);
      $items->fill($form)->save();
      return redirect('/rest');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      $items = Restdata::find($id)->delete();
      return redirect('/rest');
  }
}
