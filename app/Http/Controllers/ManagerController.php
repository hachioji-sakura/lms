<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use Illuminate\Http\Request;
use DB;
class ManagerController extends TeacherController
{
    public $domain = "managers";
    public $table = "managers";

    public $default_image_id = 4;
    public function model(){
      return Manager::query();
    }
    public function empty_model(){
      return new Manager;
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
        $count = $this->get_ask([], $ret['item']->user_id, true);
        $ret['ask_count'] = $count;
        $lists = ['lecture_cancel', 'teacher_change', 'recess', 'unsubscribe', 'phone'];
        foreach($lists as $list){
          $count = $this->get_ask(["list" => $list], $ret['item']->user_id, true);
          $ret[$list.'_count'] = $count;
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
