<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralAttribute;
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
      $param = $this->get_param($request);
      $_table = $this->search($request, $param['select_key']);
      return view($this->domain.'.lists', $_table)
        ->with($param);
    }
    private function get_param(Request $request, $attribute_key='keys'){
      $user = $this->login_details();
      if(!empty($request->get('key'))){
        $attribute_key = $request->get('key');
      }
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      $select_key = GeneralAttribute::findKey('keys')->findVal($attribute_key)->first();
      if(!isset($select_key)){
        abort(404);
      }
      $keys = GeneralAttribute::findKey('keys')->get()->toArray();
      return [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        "user" => $user,
        "search_word"=>$request->search_word,
        "select_key"=>$attribute_key,
        "select_key_name"=>$select_key['attribute_name'],
        "keys"=>$keys,
      ];
    }
    private function search(Request $request, $attribute_key)
    {
      $items = GeneralAttribute::findKey($attribute_key);
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
          "button" => ["edit", "delete"]
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
          $items = $items->where(function($items)use($search_words){
            foreach($search_words as $_search_word){
              $_like = '%'.$_search_word.'%';
              $items->orWhere('attribute_value','like',$_like)->orWhere('attribute_name','like',$_like);
            }
          });
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
     $param = $this->get_param($request);
      return view($this->domain.'.create', ["error_message" => ""])
        ->with($param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $attribute_key = $request->get('attribute_key');
      $param = $this->get_param($request, $attribute_key);

      $res = $this->_store($request);
      return $this->save_redirect($res, $param, $this->domain_name.'を登録しました', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _store(Request $request)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_responce($res)){
        return $res;
      }
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $form["create_user_id"] = $user->user_id;
        unset($form['_token']);
        unset($form['key']);
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
    public function save_validate(Request $request)
    {
      $form = $request->all();
      if(empty($form['attribute_key']) || empty($form['attribute_value']) || empty($form['attribute_name'])){
        return $this->bad_request('リクエストエラー', '属性キー='.$form['attribute_key'].'/属性値='.$form['attribute_value'].'/属性名='.$form['attribute_name']);
      }
      if(!empty($form['attribute_value_org']) && $form['attribute_value']===$form['attribute_value_org']){
        //更新時に属性値の変更なし
        return $this->api_responce(200, "", "");
      }
      $_isExist = GeneralAttribute::findKey($form['attribute_key'])->findVal($form['attribute_value'])->first();
      if($_isExist){
        return $this->error_responce('この属性は登録済みです',
          '属性キー「'.$form['attribute_key'].'」、属性値「'.$form['attribute_value'].'」の重複登録はできません');
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
      $item = GeneralAttribute::findId($id)->first();
      $attribute_key = $item['attribute_key'];
      $param = $this->get_param($request, $attribute_key);

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
      return view('components.page', [
        "_del" => $request->get('_del'),
        "item"=>$item,
        "fields"=>$fields])
        ->with($param);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      $item = GeneralAttribute::findId($id)->first();
      $attribute_key = $item['attribute_key'];
      $param = $this->get_param($request, $attribute_key);
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
      return view($this->domain.'.create', [
        "_edit" => true,
        "item"=>$item,
        "fields"=>$fields])
        ->with($param);
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
      $item = GeneralAttribute::findId($id)->first();
      $attribute_key = $item['attribute_key'];
      $param = $this->get_param($request, $attribute_key);

      $res = $this->_update($request, $id);
      return $this->save_redirect($res, $param, $this->domain_name.'を更新しました', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_responce($res)){
        return $res;
      }
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $form = $request->all();
        $item = GeneralAttribute::findId($id)->update([
          'attribute_name' => $form['attribute_name'],
          'attribute_value' => $form['attribute_value'],
        ]);
        DB::commit();
        return $this->api_responce(200, "", "", $item);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      $item = GeneralAttribute::findId($id)->first();
      $attribute_key = $item['attribute_key'];
      $param = $this->get_param($request, $attribute_key);

      $res = $this->_delete($request, $id);
      return $this->save_redirect($res, $param, $this->domain_name.'を削除しました', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _delete(Request $request, $id)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $items = GeneralAttribute::findId($id)->delete();
        DB::commit();
        return $this->api_responce(200, "", "", $items);
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
}
