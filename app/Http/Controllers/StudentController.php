<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class StudentController extends UserController
{
  public $domain = "students";
  public $domain_name = "生徒";
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    $user = $this->login_attribute();
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    $items = $this->search($request);
    return view($this->domain.'.lists', ['user' => $user, 'items' => $items])->with(["search_word"=>$request->search_word]);
  }
  private function search(Request $request)
  {
    $items = DB::table($this->domain)
      ->join('users', 'users.id', '=', $this->domain.'.user_id')
      ->join('images', 'images.id', '=', 'users.image_id');

    $items = $this->_search_scope($request, $items);

    $items = $this->_search_pagenation($request, $items);

    $items = $this->_search_sort($request, $items);

    $select_row = <<<EOT
      $this->domain.id,
      concat($this->domain.name_last, '', $this->domain.name_first) as name,
      concat($this->domain.kana_last, '', $this->domain.kana_first) as kana,
      images.s3_url as icon,
      $this->domain.gender,
      $this->domain.birth_day
EOT;
    $items = $items->selectRaw($select_row)->get();
    return $items->toArray();
  }
  private function _search_scope(Request $request, $items)
  {
    //ID 検索
    if(isset($request->id)){
      $items = $items->where($this->domain.'.id','=', $request->id);
    }
    //性別 検索
    if(isset($request->gender)){
      $items = $items->where($this->domain.'.gender','=', $request->gender);
    }
    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $items = $items->where($this->domain.'.name_last','like', $_like)
          ->orWhere($this->domain.'.name_first','like', $_like)
          ->orWhere($this->domain.'.kana_last','like', $_like)
          ->orWhere($this->domain.'.kana_first','like', $_like);
      }
    }

    //メールアドレス検索
    if(isset($request->email)){
      $_like = '%'.$request->email.'%';
      $items = $items->where('users.email','like', $_like);
    }

    return $items;
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $user = $this->login_attribute();
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    return view($this->domain.'.create', ["error_message" => ""]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $user = $this->login_attribute();
    if($this->is_manager_or_teacher($user->role)!==true){
      //事務・講師以外はアクセス不可
      abort(403);
    }
    $res = $this->_store($request);
    if($this->is_success_responce($res)){
      return redirect('/'.$this->domain)->with([
        'success_message' => $this->domain_name.'登録しました。'
      ]);
    }
    else {
      return view($this->domain.'.create', ["error_message" => $res["description"]]);
      return back()->with([
        'error_message' => $res["message"],
        'error_message_description' => $res["description"]
      ]);
    }
  }
  public function _store(Request $request)
  {
    $form = $request->all();
    try {
      DB::beginTransaction();
      $form["name"] = $form['name_last'].' '.$form['name_first'];
      //デフォルトのアイコンは性別と一緒にしておく
      $form["image_id"] = $form['gender'];
      $res = $this->user_create($form);
      if($this->is_success_responce($res)){
        $form['user_id'] = $res["data"]->id;
        unset($form['name']);
        unset($form['image_id']);
        unset($form['_token']);
        unset($form['password']);
        unset($form['email']);
        unset($form['password-confirm']);
        $Student = new Student;
        $_item = $Student->fill($form)->save();
        DB::commit();
        return $this->api_responce(200, "", "", $_item);
      }
      return $res;
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
    }
    catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
    }
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $user = $this->login_attribute();
    if($this->is_student($user->role)===true){
      //生徒は、自分（生徒）の内容しか見れない
      $id = $user->id;
    }
    $student = Student::find($id)->user;
    $item = $student->attributes();
    $comments = $student->target_comments;
    if($this->is_teacher($user->role)){
      //講師の場合、公開されたコメントのみ閲覧可能
      $comments = $comments->where('publiced_at', '<=' ,'current_date');
    }
    $comments = $comments->sortByDesc('created_at');

    foreach($comments as $comment){
      $create_user = $comment->create_user->attributes();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_icon = $create_user->icon;
      unset($comment->create_user);
    }
    $use_icons = DB::table('images')
      ->where('create_user_id','=',$user->user_id)
      ->orWhere('publiced_at','<=', date('Y-m-d'))
      ->get(['id', 'alias', 's3_url']);
    return view($this->domain.'.page', [
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
    $user = $this->login_attribute();
    if($this->is_manager_or_teacher($user->role)!==true){
      //事務・講師以外は、自分（生徒）の内容しか見れない
      $items = Student::find($id)->where("user_id", "=", $user->user_id);
    }
    else {
      $items = Student::find($id);
    }

    return view($this->domain.'.create', ['form' => $items]);
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
    $user = $this->login_attribute();
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
    return redirect('/'.$this->domain.'/'.$id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = $this->login_attribute();
    if($this->is_manager($user->role)!==true){
      //事務以外は、削除できない
      abort(403);
    }
    else {
      $items = Student::find($id);
    }
    $items->delete();
    return redirect('/'.$this->domain);
  }
}
