<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralAttribute;
use DB;
class GeneralAttributeController extends UserController
{
    public $domain = "attributes";
    public $table = "general_attributes";
    public function model(){
      return GeneralAttribute::query();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
      if(!$request->has('_line')){
        $request->merge([
          '_line' => $this->pagenation_line,
        ]);
      }
      if(!$request->has('_page')){
        $request->merge([
          '_page' => 1,
        ]);
      }
      else if($request->get('_page')==0){
        $request->merge([
          '_page' => 1,
        ]);
      }

      $param = $this->get_param($request);
      $items = $this->model();
      $items = $this->search($request, $request->get('select_key'));

      $param['items'] = $items['items'] ;
      $param['fields'] = $items['fields'] ;

      return view($this->domain.'.lists')
        ->with($param);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function api_index(Request $request, $select_key="")
    {
      $_table = $this->search($request, $select_key);
      $ret = [];
      foreach($_table['items'] as $row){
        if(!array_key_exists($row['attribute_key'], $ret)){
          $ret[$row['attribute_key']] = [];
        }
        $ret[$row['attribute_key']][] = [
          'value' => $row['attribute_value'],
          'name' => $row['attribute_name'],
        ];
      }
      return $_table['items'];
    }
    private function get_param(Request $request, $attribute_key='keys'){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      if($this->is_manager_or_teacher($user->role)!==true){
        abort(403);
      }
      if(!empty($request->get('select_key'))){
        $attribute_key = $request->get('select_key');
      }
      $select_key = GeneralAttribute::findKey('keys')->findVal($attribute_key)->first();
      if(!isset($select_key)){
        abort(404);
      }
      $keys = GeneralAttribute::findKey('keys')->get()->toArray();
      $ret = $this->get_common_param($request);
      $ret['keys'] = $keys;
      $ret['select_key'] = $select_key->attribute_value;
      $ret['select_key_name'] = $select_key->attribute_name;
      return $ret;
    }
    private function search(Request $request, $attribute_key)
    {
      $param = $this->get_param($request);

      if(empty($attribute_key)){
        $items = GeneralAttribute::query();
      }
      else {
        $items = GeneralAttribute::findKey($attribute_key);
      }
      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);
      $items = $this->_search_sort($request, $items);
      $items = $items->paginate($param['_line']);
      $fields = [
        "attribute_value" => [
          "label" => "値",
        ],
        "attribute_name" => [
          "label" => "名称",
          "link" => "show",
        ],
        "sort_no" => [
          "label" => "並順",
        ],
        "created_at" => [
          "label" => __('labels.add_datetime'),
        ],
        "updated_at" => [
          "label" => __('labels.upd_datetime'),
        ],
        "buttons" => [
          "label" => __('labels.control'),
          "button" => ["edit", "delete"]
        ]
      ];
      return ["items" => $items, "fields" => $fields];
    }
    private function _search_scope(Request $request, $items)
    {
      //ID 検索
      if(isset($request->id)){
        $items = $items->where('id',$request->id);
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
      return $this->save_redirect($res, $param, '登録しました。', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _store(Request $request)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      return $this->transaction($request, function() use ($request){
        $form = $request->all();
        $user = $this->login_details($request);
        $item = GeneralAttribute::create([
          'attribute_key' => $form['attribute_key'],
          'attribute_name' => $form['attribute_name'],
          'attribute_value' => $form['attribute_value'],
          'sort_no' => $form['sort_no'],
          'create_user_id' => $user->user_id,
        ]);
        return $this->api_response(200, '', '', $item);
      }, '汎用コード登録', __FILE__, __FUNCTION__, __LINE__ );
    }
    public function save_validate(Request $request)
    {
      $form = $request->all();
      if(empty($form['attribute_key']) || empty($form['attribute_value']) || empty($form['attribute_name'])){
        return $this->bad_request('リクエストエラー', '属性キー='.$form['attribute_key'].'/属性値='.$form['attribute_value'].'/属性名='.$form['attribute_name']);
      }
      if(!empty($form['attribute_value_org']) && $form['attribute_value']===$form['attribute_value_org']){
        //更新時に属性値の変更なし
        return $this->api_response(200, "", "");
      }
      $_isExist = GeneralAttribute::findKey($form['attribute_key'])->findVal($form['attribute_value'])->first();
      if($_isExist){
        return $this->error_response('この属性は登録済みです',
          '属性キー「'.$form['attribute_key'].'」、属性値「'.$form['attribute_value'].'」の重複登録はできません');
      }
      return $this->api_response(200, "", "");
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
          "label" => __('labels.add_datetime'),
        ],
        "updated_at" => [
          "label" => __('labels.upd_datetime'),
        ]
      ];
      return view('components.page', [
        'action' => $request->get('action'),
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
          "label" => __('labels.add_datetime'),
        ],
        "updated_at" => [
          "label" => __('labels.upd_datetime'),
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
      return $this->save_redirect($res, $param, '更新しました。', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      return $this->transaction($request, function() use ($request, $id){
        $user = $this->login_details($request);
        $form = $request->all();
        $item = GeneralAttribute::where('id', $id)->first();
        $item->update([
          'attribute_name' => $form['attribute_name'],
          'attribute_value' => $form['attribute_value'],
          'sort_no' => $form['sort_no'],
        ]);
        return $this->api_response(200, '', '', $item);
      }, '汎用コード更新', __FILE__, __FUNCTION__, __LINE__ );
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
      return $this->save_redirect($res, $param, '削除しました。', '/'.$this->domain.'?key='.$param['select_key']);
    }
    public function _delete(Request $request, $id)
    {
      return $this->transaction($request, function() use ($request, $id){
        $user = $this->login_details($request);
        $form = $request->all();
        $item = GeneralAttribute::where('id', $id)->first();
        $item->delete();
        return $this->api_response(200, '', '', $item);
      }, '汎用コード削除', __FILE__, __FUNCTION__, __LINE__ );
    }
}
