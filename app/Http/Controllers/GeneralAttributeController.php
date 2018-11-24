<?php

namespace App\Http\Controllers;

use App\Models\GeneralAttribute;
use Illuminate\Http\Request;
use DB;
class GeneralAttributeController extends UserController
{
    public $domain = "attributes";
    public $table = "general_attributes";
    public $domain_name = "定義属性";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
      $user = $this->login_details();
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      $_table = $this->search($request);
      $_table['user'] = $user;

      return view($this->domain.'.lists', $_table)->with(["search_word"=>$request->search_word]);
    }
    private function search(Request $request)
    {
      $items = GeneralAttribute::where('attribute_key','=','subject');

      $items = $this->_search_scope($request, $items);

      $items = $this->_search_pagenation($request, $items);

      $items = $this->_search_sort($request, $items);

      $items = $items->get();
      $fields = [
        "attribute_value" => [
          "label" => "値",
        ],
        "attribute_name" => [
          "label" => "名称",
          "link" => "show",
        ],
        "created_at" => [
          "label" => "登録日時",
        ],
        "updated_at" => [
          "label" => "更新日時",
        ],
        "buttons" => [
          "label" => "操作",
          "button" => ["edit", "copy", "delete"]
        ]
      ];
      return ["items" => $items->toArray(), "fields" => $fields];
    }
    private function _search_scope(Request $request, $items)
    {
      //ID 検索
      if(isset($request->id)){
        $items = $items->where('id','=', $request->id);
      }
      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $items = $items->where('attribute_value','like', $_like)
            ->orWhere('attribute_name','like', $_like);
        }
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
      if($this->is_manager_or_teacher($user->role)!==true){
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
        return back()->with([
          'error_message' => $res["message"],
          'error_message_description' => $res["description"]
        ]);
      }
    }
    public function _store(Request $request)
    {
      $res = $this->store_validate($request);
      if(!$this->is_success_responce($res)){
        return $res;
      }
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $form["create_user_id"] = $user->user_id;
        unset($form['_token']);
        $_item = GeneralAttribute::create($form);
        DB::commit();
        return $this->api_responce(200, "", "", $_item);
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
    public function store_validate(Request $request)
    {
      $form = $request->all();
      if(empty($form['attribute_key']) || empty($form['attribute_value']) || empty($form['attribute_name'])){
        return $this->bad_request();
      }
      $_isExist = GeneralAttribute::findKey($form['attribute_key'])->findVal($form['attribute_value'])->first();
      if($_isExist){
        return $this->error_responce('この属性は登録済みです', "属性キー、属性値の重複登録はできません");
      }
      return $this->api_responce(200, "", "");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $user = $this->login_details();
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      $item = GeneralAttribute::find($id)->first();
      $fields = [
        "attribute_key" => [
          "label" => "属性キー",
        ],
        "attribute_value" => [
          "label" => "属性値",
        ],
        "attribute_name" => [
          "label" => "名称",
        ],
        "created_at" => [
          "label" => "登録日時",
        ],
        "updated_at" => [
          "label" => "更新日時",
        ]
      ];
      return view($this->domain.'.page', [
        "user"=>$user,
        "item"=>$item,
        "fields"=>$fields])
        ->with(["search_word"=>$request->search_word]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
