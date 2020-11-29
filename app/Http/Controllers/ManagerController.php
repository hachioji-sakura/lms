<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use Illuminate\Http\Request;
use App\Models\Ask;
use DB;
class ManagerController extends TeacherController
{
    public $domain = "managers";
    public $table = "managers";

    public $default_image_id = 4;
    public function model(){
      return Manager::query();
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $id=null){
      $ret = $this->get_common_param($request);
      $user = $ret['user'];
      if(is_numeric($id) && $id > 0){
        //id指定がある
        if(!$this->is_manager($user->role) && $id!=$user->id){
          //講師は自分のidしか閲覧できない
          abort(403);
        }
        $ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
        $count = $this->get_ask(['user_id'=>$ret['item']->user_id], true);
        $ret['ask_count'] = $count;
        $lists = ['lecture_cancel', 'teacher_change', 'recess', 'unsubscribe', 'phone','agreement_update'];
        foreach($lists as $list){
          if($list == "agreement_update"){
            $count = Ask::findTypes(['agreement_update'])->findStatuses(['new'])->count();
          }else{
            $count = $this->get_ask(["list" => $list, 'user_id'=> $ret['item']->user_id], true);
          }

          $ret[$list.'_count'] = $count;
        }
        $statuses = ['new'];
        foreach($statuses as $status){
          $ret[$status. '_agreements_count'] = $this->get_agreements(['user_id' => $ret['item']->user_id],$status,true);
        }

      }
      else {
        //id指定がない、かつ、事務以外はNG
        if($this->is_manager($user->role)!==true){
          abort(403);
        }
      }
      return $ret;
    }
    public function login(){
      return view('managers.login');
    }
}
