<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlaceFloor;
use App\Models\PlaceFloorSheat;

class PlaceFloorController extends PlaceController
{
  public $domain = "place_floors";
  public function model(){
    return PlaceFloor::query();
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
      "sort_no" => [
        "label" => "表示順",
        'size' => 2
      ],
      "is_use" => [
        "label" => "データ利用",
        'size' => 2
      ],

      "sheat_count" => [
        "label" => "席数",
        'size' => 12
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
      ],
      "name_en" => [
        "label" => "英語名",
      ],
      "sheat_count" => [
        "label" => "席数",
      ],
      "sort_no" => [
        "label" => "表示順",
      ],
      "updated_date" => [
        "label" => __('labels.upd_datetime'),
      ],
      "buttons" => [
        "label" => __('labels.control'),
        "button" => [
          "edit",
          "delete"]
        ],
    ];
    return $fields;
  }
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['place_id'] = $request->get('place_id');
    $form['sort_no'] = $request->get('sort_no');
    $form['name'] = $request->get('name');
    $form['name_en'] = $request->get('name_en');
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
    if(empty($form['name']) || empty($form['name_en'])  || empty($form['place_id']) || empty($form['sheat_count'])){
      return $this->bad_request('リクエストエラー', 'name='.$form['name'].'/name_en='.$form['name_en'].'/place_id='.$form['place_id'].'/sheat_count='.$form['sheat_count']);
    }
    return $this->api_response(200, '', '');
  }
  public function update_form(Request $request){
    $form = [];
    $form['sort_no'] = $request->get('sort_no');
    $form['name'] = $request->get('name');
    $form['name_en'] = $request->get('name_en');
    return $form;
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
    //ID 検索
    if(isset($request->place_id)){
      $items = $items->where('place_id',$request->place_id);
    }
    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }
    return $items;
  }
  public function _store(Request $request)
  {
    $form = $this->create_form($request);
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    $item = $this->model();
    foreach($form as $key=>$val){
      $item = $item->where($key, $val);
    }
    $item = $item->first();
    if(isset($item)){
      return $this->error_response('すでに登録済みです');
    }

    $res = $this->transaction($request, function() use ($request, $form){
      $item = $this->model()->create($form);
      if($item->sheat_count < $request->get('sheat_count')){
        for($i=0;$i<intval($request->get('sheat_count')) - $item->sheat_count ;$i++){
          PlaceFloorSheat::create([
            'place_floor_id' => $item->id,
            'name' => ($i+1),
            'sort_no' => ($i+1)
          ]);
        }
      }
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
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
       $delete_count = $item->sheat_count - $request->get('sheat_count');
       if($delete_count > 0){
         foreach($item->sheats as $sheat){
           //未使用のデータならば削除可能
           if($sheat->is_use() == false) {
             echo '[id='.$sheat->id.':削除]';
             $delete_count--;
           }
         }
         if($delete_count > 0){
           return $this->error_response('使用中のデータがあるため、席数を減らすことはできません');
         }
       }
       $item->update($form);
       if($item->sheat_count < $request->get('sheat_count')){
         for($i=0;$i<intval($request->get('sheat_count')) - $item->sheat_count ;$i++){
           PlaceFloorSheat::create([
             'place_floor_id' => $item->id,
             'name' => ($i+1),
             'sort_no' => ($i+1)
           ]);
         }
       }
       else if($item->sheat_count > $request->get('sheat_count')){
         $delete_count = $item->sheat_count - $request->get('sheat_count');
         foreach($item->sheats as $sheat){
           //未使用のデータならば削除可能
           if($sheat->is_use() == false){
             $sheat->delete();
             $delete_count--;
           }
           if($delete_count < 1) break;
         }
       }
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }

}
