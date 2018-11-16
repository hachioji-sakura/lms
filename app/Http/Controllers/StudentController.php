<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Image;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $user = $this->get_login_user();
        if($this->is_manager_or_teacher($user->role)!==true){
          abort(403);
        }
        $items = $this->search($request);
        return view('students.lists', ['user' => $user, 'items' => $items])->with(["search_word"=>$request->search_word]);
    }
    protected function search(Request $request)
    {
      $user = $this->get_login_user();
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      $items = DB::table('students')
        ->join('users', 'users.id', '=', 'students.user_id')
        ->join('images', 'images.id', '=', 'users.image_id');
      //ID 検索
      if(isset($request->id)){
        $items = $items->where('students.id','=', $request->id);
      }
      //性別 検索
      if(isset($request->gender)){
        $items = $items->where('students.gender','=', $request->gender);
      }
      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $items = $items->where('students.name_last','like', $_like)
            ->orWhere('students.name_first','like', $_like)
            ->orWhere('students.kana_last','like', $_like)
            ->orWhere('students.kana_first','like', $_like);
        }
      }

      //メールアドレス検索
      if(isset($request->email)){
        $_like = '%'.$request->email.'%';
        $items = $items->where('users.email','like', $_like);
      }

      //ページング
      $_line = 20;
      if(isset($request->_line)){
        $_line = $request->_line;
      }
      if(isset($request->_page)){
        $_offset = ($request->_page-1)*$_line;
        if($_offset < 0) $_offset = 0;
        $items = $items->offset($_offset);
        $items = $items->limit($_line);
      }
      //ソート
      if(isset($request->_sort)){
        $_sort_order = "asc";
        if(isset($request->_sort_order) && $request->_sort_order==="desc") {
          $_sort_order = "desc";
        }
        $items = $items->orderBy($request->_sort, $_sort_order);
      }
      $items = $items->get([
        'students.id',
        'students.name_last',
        'students.name_first',
        'students.kana_last',
        'students.kana_first',
        'images.s3_url as icon',
        'users.email',
        'students.gender',
        'students.birth_day',
      ]);

      return $items->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $user = $this->get_login_user();
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      return view('students.create', ["error_message" => ""]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = $this->get_login_user();
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = User::create([
            'name' => $form['name_last'].' '.$form['name_first'],
            'email' => $form['email'],
            'image_id' => $form['gender'],
            'password' => Hash::make($form['password']),
        ]);
        $Student = new Student;
        $form['user_id'] = $user->id;
        unset($form['_token']);
        unset($form['password']);
        unset($form['email']);
        unset($form['password-confirm']);
        $Student->fill($form)->save();
        DB::commit();
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return view('students.create', ["error_message" => "登録に失敗しました。"]);
      }
      catch(\Exception $e){
          DB::rollBack();
          return view('students.create', ["error_message" => "登録に失敗しました。"]);
      }
      return redirect('/students');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $user = $this->get_login_user();
      if($this->is_student($user->role)===true){
        //生徒は、自分（生徒）の内容しか見れない
        $id = $user->student_id;
      }
      $stuendt = Student::find($id)->user;
      $item = $stuendt->getData();
      $comments = $stuendt->target_comments;
      if($this->is_teacher($user->role)){
        //講師の場合、公開されたコメントのみ閲覧可能
        $comments = $comments->where('publiced_at', '<=' ,'current_date');
      }
      $comments = $comments->sortByDesc('created_at');

      foreach($comments as $comment){
        $create_user = $comment->create_user->getData();
        $comment->create_user_name = $create_user->name;
        $comment->create_user_icon = $create_user->icon;
        unset($comment->create_user);
      }
      $use_icons = DB::table('images')
        ->where('create_user_id','=',$user->user_id)
        ->orWhere('publiced_at','<=','current_date')
        ->get(['id', 'alias', 's3_url']);
      return view('students.page', [
        'user' => $user,
        'item' => $item,
        'comments'=>$comments,
        'use_icons'=>$use_icons,
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user = $this->get_login_user();
      if($this->is_manager_or_teacher($user->role)!==true){
        //事務・講師以外は、自分（生徒）の内容しか見れない
        $items = Student::find($id)->where("user_id", "=", $user->user_id);
      }
      else {
        $items = Student::find($id);
      }

      return view('students.create', ['form' => $items]);
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
      $user = $this->get_login_user();
      if($this->is_manager_or_teacher($user->role)!==true){
        //事務・講師以外は、自分（生徒）の内容しか更新できない
        $items = Student::find($id)->where("user_id", "=", $user->id);
      }
      else {
        $items = Student::find($id);
      }
      $form = $request->all();
      unset($form['_token']);
      $items->fill($form)->save();
      return redirect('/students/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = $this->get_login_user();
      if($this->is_manager($user->role)!==true){
        //事務以外は、削除できない
        abort(403);
      }
      else {
        $items = Student::find($id);
      }
      $items->delete();
      return redirect('/students');
    }
    /**
     * 認証済みユーザーのデータを取得
     *
     * @return Collection User->getData()
    */
    protected function get_login_user()
    {
      $user = Auth::user();
      if(!isset($user)){
        abort(403);
        return "";
      }
      return $user->getData();
    }

    /**
      * roleが事務の場合 true
      * @param string role
      * @return boolean
    */
    protected function is_manager($role)
    {
      if($role==="manager"){
        return true;
      }
      return false;
    }
    /**
      * roleが講師の場合 true
      * @param string role
      * @return boolean
    */
    protected function is_teacher($role)
    {
      if($role==="teacher"){
        return true;
      }
      return false;
    }
    /**
      * roleが生徒の場合 true
      * @param string role
      * @return boolean
    */
    protected function is_student($role)
    {
      if($role==="student"){
        return true;
      }
      return false;
    }
    /**
      * roleが事務、もしくは講師の場合 true
      * @param string role
      * @return boolean
    */
    protected function is_manager_or_teacher($role)
    {
      if($role==="manager" || $role==="teacher"){
        return true;
      }
      return false;
    }

}
