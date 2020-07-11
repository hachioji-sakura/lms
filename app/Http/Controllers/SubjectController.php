<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectController extends CurriculumController
{
  public $domain = 'subjects';
  public function model(){
    return Subject::query();
  }

  public function create(Request $request){
    $param = $this->get_param($request);
    $param['_edit'] = false;
    return view($this->domain.'.create')->with($param);
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


}
