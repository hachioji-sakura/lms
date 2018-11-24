<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Manager;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class TeacherController extends UserController
{
  public $domain = "teachers";
  public $table = "teachers";
  public $domain_name = "講師";
  public $default_image_id = 3;
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    $user = $this->login_details();
    if($this->is_manager($user->role)!==true){
      abort(403);
    }
    $items = $this->search($request);
    return view($this->domain.'.lists', ['user' => $user, 'items' => $items])->with(["search_word"=>$request->search_word]);
  }
  private function search(Request $request)
  {
    $items = DB::table($this->table)
      ->join('users', 'users.id', '=', $this->table.'.user_id')
      ->join('images', 'images.id', '=', 'users.image_id');

    $items = $this->_search_scope($request, $items);

    $items = $this->_search_pagenation($request, $items);

    $items = $this->_search_sort($request, $items);

    $select_row = <<<EOT
      $this->table.id,
      $this->table.name,
      $this->table.kana,
      images.s3_url as icon
EOT;
    $items = $items->selectRaw($select_row)->get();
    return $items->toArray();
  }
  private function _search_scope(Request $request, $items)
  {
    //ID 検索
    if(isset($request->id)){
      $items = $items->where($this->table.'.id','=', $request->id);
    }
    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $items = $items->where($this->table.'.name','like', $_like)
          ->orWhere($this->table.'.kana','like', $_like);
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
  public function create(Request $request)
  {
    $user = $this->login_details();
    if($this->is_manager($user->role)!==true){
      abort(403);
    }
    //return view($this->domain.'.create', ["error_message" => ""]);
    return view($this->domain.'.create', ['user' => $user, "error_message" => ""])
        ->with(["search_word"=>$request->search_word]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $user = $this->login_details();
    if($this->is_manager($user->role)!==true){
      //事務以外はアクセス不可
      abort(403);
    }
    $form = $request->all();
    $res = $this->_store($form);
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
  public function _store($form)
  {
    try {
      DB::beginTransaction();
      $form["image_id"] = $this->default_image_id;
      $res = $this->user_create($form);
      if($this->is_success_responce($res)){
        $form['user_id'] = $res["data"]->id;
        $user = $this->login_details();
        $form["create_user_id"] = $user->user_id;
        unset($form['image_id']);
        unset($form['_token']);
        unset($form['password']);
        unset($form['email']);
        unset($form['password-confirm']);
        if($this->domain === "teachers"){
          $model = new Teacher();
        }
        else if($this->domain === "managers"){
          $model = new Manager();
        }
        $_item = $model->fill($form)->save();
        DB::commit();
        return $this->api_responce(200, "", "", $_item);
      }
      return $res;
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
    catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
    }
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $user = $this->login_details();
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    if($this->domain === "teachers"){
      if($this->is_teacher($user->role)===true){
          $id = $user->id;
      }
      $model = Teacher::find($id)->user;
    }
    else if($this->domain === "managers"){
      if($this->is_teacher($user->role)===true){
        abort(403);
      }
      $model = Manager::find($id)->user;
    }
    $item = $model->details();
    $comments = $model->target_comments;
    $comments = $comments->sortByDesc('created_at');
    foreach($comments as $comment){
      $create_user = $comment->create_user->details();
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
    $target = Teacher::find($id);
    return view($this->domain.'.create',  ['form' => $target]);

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
      return redirect('/'.$this->domain);
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
