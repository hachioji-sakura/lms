<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Teacher::all();
        return $items->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auth.teacher_create', ["error_message" => ""]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = User::create([
            'name' => $form['name'],
            'email' => $form['email'],
            'image_id' => 3,
            'password' => Hash::make($form['password']),
        ]);
        $Teacher = new Teacher;
        $form['user_id'] = $user->id;
        unset($form['_token']);
        unset($form['password']);
        unset($form['email']);
        unset($form['password-confirm']);
        $Teacher->fill($form)->save();
        DB::commit();
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return view('auth.teacher_create', ["error_message" => "登録に失敗しました。"]);
      }
      catch(\Exception $e){
          DB::rollBack();
          return view('auth.teacher_create', ["error_message" => "登録に失敗しました。"]);
      }
      return redirect('/teachers');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $items = Teacher::find($id);
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
      $target = Teacher::find($id);
      return view('auth.teacher_create',  ['form' => $target]);

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
        $items = Teacher::find($id);
        $form = $request->all();
        unset($form['_token']);
        $items->fill($form)->save();
        return redirect('/teachers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $items = Teacher::find($id)->delete();
        return redirect('/teachers');
    }
}
