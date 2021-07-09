<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends MilestoneController
{
  public $domain = "places";
  public function model(){
    return Place::query();
  }
  public function show_fields($type=''){
    $fields = [
      "name" => [
        "label" => "名称",
        'size' => 4
      ],
      "name_en" => [
        "label" => "英語名",
        'size' => 4
      ],
      "status_name" => [
        "label" => "ステータス"
      ],
      "sort_no" => [
        "label" => "表示順",
        'size' => 2
      ],
      "is_use" => [
        "label" => "データ利用",
        'size' => 2
      ],
      "post_no" => [
        "label" => "郵便番号",
        'size' => 4
      ],
      "address" => [
        "label" => '所在地',
        'size' => 8
      ],
      "phone_no" => [
        "label" => '連絡先',
      ],
    ];
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
      'size' => 6
    ];
    $fields['updated_date'] = [
      'label' => __('labels.upd_datetime'),
      'size' => 6
    ];
    return $fields;
  }
  public function list_fields(){
    $fields = [
      'id' => [
        'label' => 'ID',
        "link" => "show",
      ],
      "name" => [
        "label" => "名称",
        "link" =>  function($row){
          return "/place_floors?place_id=".$row->id;
        },
      ],
      "name_en" => [
        "label" => "英語名",
      ],
      "status_name" => [
        "label" => "ステータス"
      ],
      "sort_no" => [
        "label" => "表示順",
      ],
      "post_no" => [
        "label" => "郵便番号",
      ],
      "address" => [
        "label" => '所在地',
      ],
      "phone_no" => [
        "label" => '連絡先',
      ],
      "updated_date" => [
        "label" => __('labels.upd_datetime'),
      ],
      "buttons" => [
        "label" => __('labels.control'),
        "button" => [
          'place_textbooks' =>[
            'style' =>'outline-primary',
            'label' => '使用テキスト',
            "link" => function($row){
              return "/place_textbooks?place_id=".$row['id'];
            }
          ],
          "edit",
          ]
        ],
    ];
    return $fields;
  }

  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['address'] = $request->get('address');
    $form['phone_no'] = $request->get('phone_no');
    $form['post_no'] = $request->get('post_no');
    $form['sort_no'] = $request->get('sort_no');
    $form['name'] = $request->get('name');
    $form['name_en'] = $request->get('name_en');
    $form['status'] = $request->get('status');
    return $form;
  }
  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    $form = $request->all();
    //保存時にパラメータをチェック
    if(empty($form['name']) || empty($form['name_en'])){
      return $this->bad_request('リクエストエラー', 'name='.$form['name'].'/name_en='.$form['name_en']);
    }
    return $this->api_response(200, '', '');
  }
  public function update_form(Request $request){
    $form = [];
    $form['address'] = $request->get('address');
    $form['phone_no'] = $request->get('phone_no');
    $form['post_no'] = $request->get('post_no');
    $form['sort_no'] = $request->get('sort_no');
    $form['name'] = $request->get('name');
    $form['name_en'] = $request->get('name_en');
    $form['status'] = $request->get('status');
    return $form;
  }
  public function get_param(Request $request, $id=null){
    $ret = parent::get_param($request, $id);
    if($this->is_manager($ret['user']->role)!==true){
      abort(403);
    }
    if($request->has('place_id')){
      $ret['place_id'] = $request->get('place_id');
    }
    return $ret;
  }
  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request)
  {
    $param = $this->get_param($request);
    $request->merge([
      '_sort_order' => 'asc',
      '_sort' => 'sort_no',
    ]);
    if($request->has('is_desc') && $request->get('is_desc')==1){
      $request->merge([
        '_sort_order' => 'asc',
      ]);
    }
    $items = $this->model();
    $user = $this->login_details($request);
    $items = $this->_search_scope($request, $items);
    $items = $this->_search_sort($request, $items)->paginate($param['_line']);

    return ['items' => $items, 'fields' => $this->list_fields()];
  }
  /**
   * フィルタリングロジック
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  Collection $items
   * @return Collection
   */
  public function _search_scope(Request $request, $items)
  {
    //ID 検索
    if(isset($request->id)){
      $items = $items->where('id',$request->id);
    }
    if(isset($request->post_no)){
      $items = $items->where('post_no', 'like', $request->post_no.'%');
    }

    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }

    return $items;
  }

   public function _update(Request $request, $id)
   {
     $res = $this->save_validate($request);
     if(!$this->is_success_response($res)){
       return $res;
     }
     $res =  $this->transaction($request, function() use ($request, $id){
       $form = $this->update_form($request);
       $item = $this->model()->where('id', $id)->first();
       $item->update($form);
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function _delete(Request $request, $id)
   {
     $form = $request->all();
       $item = $this->model()->where('id', $id)->first();
       $ret = $item->dispose();
       if(!$this->is_success_response($ret)) return $ret;
       return $this->api_response(200, '', '', $item);
     $res = $this->transaction($request, function() use ($request, $form, $id){
     }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }

   public function phone_list(Request $request){
     $param = parent::get_param($request);
     //TODO:電話番号一覧用にデータを新たに定義する必要がある
     //校舎だけでなくサポートのような存在が必要になるため
     $param['items'] = $this->model()->hasPhoneNo()->enable()->get();//enableで電話番号を持つ校舎をすべて表示する
     $param['fields'] = $this->get_phone_fields();
     return view($this->domain.'.phone_list')->with($param);
   }

   public function get_phone_fields(){
     return [
       "name" => [
         'label' => '教室名',
       ],
       "phone_no" => [
         'label' => '電話番号',
       ],
     ];
   }
}
