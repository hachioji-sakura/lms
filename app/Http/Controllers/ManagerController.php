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
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $id=null){
      $id = intval($id);
      $user = $this->login_details($request);
      $pagenation = '';
      $ret = [
        'domain' => $this->domain,
        'domain_name' => __('labels.'.$this->domain),
        'user' => $user,
        'mode'=>$request->mode,
        'search_word'=>$request->get('search_word'),
        '_status' => $request->get('status'),
        '_page' => $request->get('_page'),
        '_line' => $request->get('_line'),
        'list' => $request->get('list'),
        'attributes' => $this->attributes(),
      ];
      $ret['filter'] = [
        'is_unchecked' => $request->is_unchecked,
        'is_asc'=>$request->is_asc,
        'is_desc'=>$request->is_desc,
        'search_keyword'=>$request->search_keyword,
        'search_comment_type'=>$request->search_comment_type,
        'search_week'=>$request->search_week,
        'search_work' => $request->search_work,
        'search_place' => $request->search_place,
      ];
      if(empty($ret['_line'])) $ret['_line'] = $this->pagenation_line;
      if(empty($ret['_page'])) $ret['_page'] = 0;
      if(empty($user)){
        //ログインしていない
        abort(419);
      }
      if(is_numeric($id) && $id > 0){
        //id指定がある
        if(!$this->is_manager($user->role) && $id!==$user->id){
          //講師は自分のidしか閲覧できない
          abort(403);
        }
        $ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
        $asks = $this->get_ask([], $ret['item']->user_id);
        $ret['ask_count'] = $asks["count"];
        $lists = ['lecture_cancel', 'teacher_change', 'recess', 'unsubscribe', 'phone'];
        foreach($lists as $list){
          $asks = $this->get_ask(["list" => $list], $ret['item']->user_id);
          $ret[$list.'_count'] = $asks["count"];
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
