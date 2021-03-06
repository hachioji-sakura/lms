<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TextMaterial;
use Illuminate\Support\Facades\Auth;


class CurriculumController extends MilestoneController
{
    public $domain = "curriculums";
    public function model(){
      return Curriculum::query();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $param = $this->get_param($request);
        $items = $this->model()->search($request)->paginate($param['_line']);

        $param['items'] = $items;
        $param['subjects'] = Subject::all();
        $param['search_subject_id'] = $request->get('search_subject_id');
        return view($this->domain.'.list')->with($param);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function api_index(Request $request)
    {
        //
        $param = $this->get_common_param($request, false);
        $items = $this->model()->search($request)->get();
        return $this->api_response(200, '', '', $items);
    }

    public function create(Request $request){

      $param = $this->get_param($request);
      $subjects = Subject::all();
      $param['subjects'] = $subjects;
      $param['_edit'] = false;
      return view($this->domain.'.create')->with($param);
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

      //同一科目内で同一名称がつかないようにチェック
      $subjects = Subject::find($request->get('subject_ids'));
      $items = collect([]);
      foreach($subjects as $subject){
        $items[] = $subject->curriculums()->where('name', $request->get('name'))->count();
      }
      if($items->reject(function($item){return $item == 0;})->count() > 0){
        return $this->error_response('すでに登録済みです');
      }
      $res = $this->transaction($request, function() use ($request, $form){
        $item = $this->model()->create($form);
        $item->subjects()->attach($request->get('subject_ids'));
        return $this->api_response(200, '', '', $item);
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    public function create_form(Request $request){
      $form = [
        'name' => $request->get('name'),
        'remarks' => $request->get('remarks'),
        'create_user_id' => Auth::user()->id,
      ];
      return $form;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, $id = null)
    {
        //
        $param = $this->get_param($request, $id);
        return view($this->domain.'.details')->with($param);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function edit(Request $request, $id)
     {
       $param = $this->get_param($request, $id);
       $subjects = Subject::all();
       $param['subjects'] = $subjects;
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
         $is_file_delete = false;
         if($request->get('upload_file_delete')==1){
           $is_file_delete = true;
         }
         $item->update_curriculum($form,$request->get('subject_ids'));
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
       ];
       return $form;
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

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

    public function name_check(Request $request, $name){
      $item = Curriculum::where('name', $name)->SearchBySubjectId($request->get('subject_id'))->first();
      if(isset($item)){
        $json = $this->api_response(200,"","",["name"=>$name]);
        return $this->send_json_response($json);
      }
      return $this->send_json_response($this->notfound());
    }

    public function get_select_list(Request $request){
      $param = $this->get_param($request);
      if(!empty($request->get('task_id'))){
        $param['item'] = Task::find($request->get('task_id'));
      }
      $curriculums = Subject::find($request->get('subject_id'))->curriculums;
      $param['curriculums'] = $curriculums;
      $param['_edit'] = true;
      return view('curriculums.components.select_list')->with($param);
    }
}
