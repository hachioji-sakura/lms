<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Student;
use App\Models\Place;
use Auth;

class OrderController extends MilestoneController
{
    //
    public $domain = "orders";
    public $domain_name = "発注";
    public $_line = 20;
    public function model(){
      return Order::query();
    }

    public function search(Request $request){
      $param = $this->get_param($request);
      $items = $this->model()->search($request)->paginate($this->_line);
      $fields = $this->get_list_fields();
      return ['items' => $items, 'fields' => $fields];
    }

    public function get_list_fields(){
      return [
        'title' => [
          'label' => __('labels.title'),
          'link' => 'show',
        ],
        'status_name' => [
          'label' => __('labels.status')
        ],
        'target_user_name' => [
          'label' => __('labels.target_user')
        ],
        'ordered_user_name' => [
          'label' => __('labels.ordered_user')
        ],
        'order_buttons' => [
          'label' => __('labels.control'),
          'include' => 'buttons',
        ]
      ];
    }

    public function create(Request $request)
    {
       $param = $this->get_param($request);
       return view($this->domain.'.create',['_edit' => false])
         ->with($param);
     }

     public function _store(Request $request){
       $form = $this->create_form($request);
       $res = $this->transaction($request, function() use ($request, $form){
         $item = $this->model()->create($form);
         return $this->api_response(200, '', '', $item);
       }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
     }

     public function create_form($request){
       return[
          "title" => $request->title,
          "status" => "new",
          "type" => $request->type,
          "target_user_id" => $request->target_user_id,
          "ordered_user_id" => Auth::user()->id,
          "place_id" => $request->place_id,
          "lesson_id" => $request->lesson_id,
          "amount" => $request->amount,
          "unit_price" => $request->unit_price,
          "item_type" => $request->item_type,
          "remarks" => $request->remarks,
       ];
     }

     public function _update(Request $request, $id){
       $res =  $this->transaction($request, function() use ($request, $id){
         $form = $this->create_form($request);
         $item = $this->model()->find($id);
         $item->update($form);
         return $this->api_response(200, '', '', $item);
       }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
       return $res;
     }

     public function show(Request $request ,$id){
       $param = $this->get_param($request,$id);
       $param['fields'] = $this->get_show_fields();
       return view('components.page')->with($param);
     }

     public function get_show_fields(){
       return [
         'status_name'=>[
           'label' => __('labels.status')
         ],
         'title' => [
           'label'=> __('labels.title')
         ],
         'target_user_name' => [
           'label' => __('labels.target_user')
         ],
         'ordered_user_name' => [
           'label' => __('labels.ordered_user')
         ],
         'place_name' => [
           'label' => __('labels.place')
         ],
         'lesson_name' => [
           'label' =>__('labels.department')
         ]
       ];
     }

     public function status_update_page(Request $request, $id, $status){
       $param = $this->get_param($request,$id);
       $param['status'] = $status;
       $param['fields'] = $this->get_show_fields();
       return view($this->domain.'.status_update')->with($param);
     }

     public function delete_page(Request $request, $id){
       $param = $this->get_param($request,$id);
       $param['fields'] = $this->get_show_fields();
       return view($this->domain.".delete")->with($param);
     }

     public function  _delete(Request $request, $id){
       $res = $this->transaction($request, function() use ($request, $id){
         $item = $this->model()->find($id);
         $item->dispose();
         return $this->api_response(200, '', '', $item);
       }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
       return $res;
     }

     public function status_update(Request $request ,$id ,$status){
       $param = $this->get_param($request,$id);
       $res =  $this->transaction($request, function() use ($id,$status){
         $item = $this->model()->find($id);
         $item->status_update($status);
         return $this->api_response(200, '', '', $item);
       }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
       return $this->save_redirect($res, $param,'更新しました');
     }

    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      $ret = $this->get_common_param($request);
      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id','=',$id)->first();
        $ret['item'] = $item;
      }
      if($request->has('teacher_id')){
        $ret['target_users'] = Student::findChargeStudent($teacher_id)->get()->pluck('name','user_id');
      }else{
        $ret['target_users'] = Student::findStatuses(['regular'])->get()->pluck('name','user_id');
      }
      $ret['places'] = Place::all()->pluck('name','id');
      return $ret;
    }
}
