<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class TaskController extends MilestoneController
{
    public $domain = 'tasks';
    public function model(){
      return Task::query();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = $this->get_param($request);
        $user = $this->login_details($request);
        return view($this->domain . '.list',$param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $param = $this->get_param($request);
        $user = $this->login_details($request);
        $param['_edit'] = false;
        return view($this->domain . '.create',$param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
        //
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
          if($request->hasFile('upload_file')){
            if ($request->file('upload_file')->isValid([])) {
              $item->file_upload($request->file('upload_file'));
            }
          }
          return $this->api_response(200, '', '', $item);
        }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
        return $res;
     }

    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = [];
      $form['title'] = $request->get('title');
      $form['remarks'] = $request->get('remarks');
      $form['milestone_id'] = $request->get('milestone_id');
      $form['type'] = $request->get('type');
      $form['status'] = 'new'; //登録時はnew
      $form['target_user_id'] = $request->get('target_user_id');
      $form['create_user_id'] = $user->id;
      $form['start_schedule'] = $request->get('start_schedule');
      $form['end_schedule'] = $request->get('end_schedule');

      return $form;
    }

    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if(empty($form['title']) || empty($form['start_schedule']) || empty($form['end_schedule']) ) {
        return $this->bad_request('リクエストエラー', 'タイトル='.$form['title'].'/開始予定='.$form['start_schedule'].'終了予定='.$form['end_schedule']);
      }
      return $this->api_response(200, '', '');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request ,$id)
    {
        //
        $param = $this->get_param($request);
        $item = $this->model()->where('id',$id)->first();
        $param['item'] = $item;
        $param['target_user'] = Student::where('user_id',$item->target_user_id)->first();
        $param['_edit'] = true;
        return view($this->domain.'.create')->with($param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $res =  $this->transaction($request, function() use ($request, $id){
        $form = $this->update_form($request);
        $item = $this->model()->where('id', $id)->first();
        $this->model()->where('id',$id)->update($form);
        /*
        $is_file_delete = false;
        if($request->get('upload_file_delete')==1){
          $is_file_delete = true;
        }
        $file = null;
        if($request->hasFile('upload_file')){
          if ($request->file('upload_file')->isValid([])) {
            $file = $request->file('upload_file');
          }
        }
        $item->change($form, $file, $is_file_delete);
        */
        return $this->api_response(200, '', '', $item);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    public function update_form(Request $request){
      $form = [];
      $form['title'] = $request->get('title');
      $form['remarks'] = $request->get('remarks');
      $form['milestone_id'] = $request->get('milestone_id');
      $form['type'] = $request->get('type');
      $form['start_schedule'] = $request->get('start_schedule');
      $form['end_schedule'] = $request->get('end_schedule');

      return $form;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _delete(Request $request, $id)
    {
        //
        $form = $request->all();
        $res = $this->transaction($request, function() use ($request, $form, $id){
          $item = $this->model()->where('id', $id)->first();
          /*
          if(isset($item['s3_url']) && !empty($item['s3_url'])){
            //S3アップロードファイルがある場合は削除
            $this->s3_delete($item['s3_url']);
          }
          */
          dd($item);
          $item->dispose();
          return $this->api_response(200, '', '', $item);
        }, '削除しました。', __FILE__, __FUNCTION__, __LINE__ );
        return $res;
    }
}
