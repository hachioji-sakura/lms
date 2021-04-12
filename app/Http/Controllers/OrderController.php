<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Student;

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
        'titel' => [
          'label' => __('labels.title')
        ]
      ];
    }

    public function create(Request $request)
    {
       $param = $this->get_param($request);
       if($request->has('teacher_id')){
         $param['target_users'] = Student::findChargeStudent($teacher_id)->get()->pluck('name','user_id');
       }else{
         $param['target_users'] = Student::findStatuses(['regular'])->get()->pluck('name','user_id');
       }

       return view($this->domain.'.create',['_edit' => false])
         ->with($param);
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
      return $ret;
    }
}
