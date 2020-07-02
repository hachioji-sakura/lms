<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SchoolController extends MilestoneController
{
    //
    public $domain = "schools";
    public function model(){
      return School::query();
    }

    public function index(Request $request)
    {
        $param = $this->get_param($request);
        $items = $this->model()->search($request)->paginate($param['_line']);
        $param['items'] = $items;
        return view($this->domain.'.list')->with($param);
    }

    public function create_form(Request $request){
      $form = [
        'name' => $request->get('name'),
        'remarks' => $request->get('remarks'),
        'hp_url' => $request->get('hp_url'),
        'sort_no' => $this->model()->max('sort_no') + 1,
        'create_user_id' => Auth::user()->id,
      ];
      return $form;
    }

    public function _store(Request $request)
    {
        //
        $form = $this->create_form($request);
        $item = $this->model();
        foreach($form as $key=>$val){
          $item = $item->where($key,$val);
        }
        $item = $item->first();
        if(isset($item)){
          return $this->error_response('すでに登録済みです');
        }

        $res = $this->transaction($request, function() use ($request, $form){
          $item = $this->model()->create($form);
          return $this->api_response(200, '', '', $item);
        }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
        return $res;
    }

    public function show( Request $request, $id = null)
    {
        //
        $param = $this->get_param($request, $id);
        return view($this->domain.'.details')->with($param);
    }


    public function edit(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $param['_edit'] = true;
      return view($this->domain.'.create')->with($param);
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
        $is_file_delete = false;
        if($request->get('upload_file_delete')==1){
          $is_file_delete = true;
        }
        $item->update($form);
        return $this->api_response(200, '', '', $item);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }



    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if(empty($form['name']) ){
        return $this->bad_request('リクエストエラー', '名前='.$form['name']);
      }
      return $this->api_response(200, '', '');
    }

    public function update_form(Request $request){
      $form = [
        'name' => $request->get('name'),
        'remarks' => $request->get('remarks'),
        'hp_url' => $request->get('hp_url'),
      ];
      return $form;
    }

    public function delete(Request $request, $id){
      $param = $this->get_param($request,$id);
      return view($this->domain.'.delete')->with($param);
    }

   public function _delete(Request $request, $id)
   {
     $form = $request->all();
     $res = $this->transaction($request, function() use ($request, $form, $id){
       $item = $this->model()->find($id);
       $item->dispose();
       return $this->api_response(200, '', '',$item);
     }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }

}
