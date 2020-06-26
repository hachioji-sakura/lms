<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Review;
use App\Models\Student;
use App\Models\Curriculum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends MilestoneController
{
    public $domain = 'tasks';
    public $pagenate_line = 20;
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
        $param['items'] = $this->model()->search($request)->paginate($param['_line']);
        $param['request'] = $request;
        return view($this->domain . '.list')->with($param);
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
        $param['target_student'] = Student::where('id', $request->get('student_id'))->first();
        $param['curriculums'] = Curriculum::all();
        $param['_edit'] = false;
        $param['task_type'] = $request->get('task_type');
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
          return $this->error_response(__('messages.task_already_registered'));
        }
        $res = $this->transaction($request, function() use ($request, $form){
          $item = $this->model()->create($form);
          $item->curriculums()->attach($request->get('curriculum_ids'));
          if($request->hasFile('upload_file')){
            if ($request->file('upload_file')->isValid([])) {
              $item->file_upload($request->file('upload_file'));
            }
          }
          return $this->api_response(200, '', '', $item);
        }, __('messages.info_add'), __FILE__, __FUNCTION__, __LINE__ );
        if($this->is_success_response($res) && $request->get('mail_send') == "yes"){
          $this->sendmail($res);
        }
        return $res;
     }

     public function sendmail($res){
       //メールを送信する
       $item = $res['data']->details();
       $template = 'task';
       $type = 'text';
       $param = ['item' => $item ];
       if($item->create_user->details()->role == "parent"){
         $title_of_honor = "様";
       }elseif($item->create_user->details()->role == "teacher"){
         $title_of_honor = "先生";
       }else{
         $title_of_honor = "さん";
       }

       $item->target_user->send_mail(__('messages.task_title',['user_name' => $item->create_user->details()->name(),'title_of_honor' => $title_of_honor]), $param, $type ,$template);
       return $res;
     }

    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = [];
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      $form['milestone_id'] = $request->get('milestone_id');
      $form['type'] = $request->get('type');
      if($form['type'] == 'class_record'){
        $form['status'] = 'done';
      }else{
        $form['status'] = 'new';
      }
      $form['target_user_id'] = $request->get('target_user_id');
      $form['create_user_id'] = $user->user_id;
      $form['start_schedule'] = $request->get('start_schedule');
      $form['end_schedule'] = $request->get('end_schedule');

      return $form;
    }

    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if(empty($form['title']) || empty($form['type']) ) {
        return $this->bad_request('リクエストエラー', 'タイトル='.$form['title'].'/タイプ='.$form['type']);
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
        $param = $this->get_param($request);
        $item = $this->model()->where('id',$id)->first();
        $param['item'] = $item;
        $param['target_student'] = $item->target_user->details();
        $param['status_count'] = $item->target_user->student->get_target_task_count();
        $param['items'] = $this->model()->findTargetUser($item->target_user->details()->user_id);
        $param['domain'] = $item->target_user->details()->domain;
        return view($this->domain. '.details')->with($param);
    }

    public function detail_dialog(Request $request, $id){
      $param = $this->get_param($request);
      $item = $this->model()->where('id',$id)->first();
      $param['item'] = $item;
      $param['target_student'] = $item->target_user->details();
      $param['status_count'] = $item->target_user->student->get_target_task_count();
      $param['items'] = $this->model()->findTargetUser($item->target_user->details()->user_id);
      $param['domain'] = $item->target_user->details()->domain;
      return view($this->domain. '.simple_details')->with($param);
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
        $param['target_student'] = Student::where('user_id',$item->target_user_id)->first();
        $param['_edit'] = true;
        $param['task_type'] = $request->get('task_type');
        $param['curriculums'] = Curriculum::all();
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
        $item->change($form, $file, $is_file_delete,$request->get('curriculum_ids'));

        return $this->api_response(200, '', '', $item);
      }, __('messages.info_updated'), __FILE__, __FUNCTION__, __LINE__ );
      if($this->is_success_response($res) && $request->get('mail_send') == "yes"){
        $this->sendmail($res);
      }
      return $res;
    }

    public function update_form(Request $request){
      $form = [];
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      $form['milestone_id'] = $request->get('milestone_id');
      $form['type'] = $request->get('type');
      $form['status'] = $request->get('status');
      $form['start_schedule'] = $request->get('start_schedule');
      $form['end_schedule'] = $request->get('end_schedule');

      return $form;
    }

    public function show_new_page(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['action'] = 'new';
      return view($this->domain.'.confirm')->with($param);
    }

    public function show_cancel_page(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['action'] = 'cancel';
      return view($this->domain.'.confirm')->with($param);
    }

    public function show_progress_page(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['action'] = 'progress';
      return view($this->domain.'.confirm')->with($param);
    }

    public function show_done_page(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['action'] = 'done';
      return view($this->domain.'.confirm')->with($param);
    }

    public function new(Request $request ,$id){
      $param = $this->get_param($request,$id);
      $form = [];
      $form['status'] = 'new';
      $form['start_date'] = null;
      $res = $this->change_status($request, $id, $form);
      return $this->save_redirect($res,$param, __('messages.task_status_new'));
    }

    public function cancel(Request $request,$id){
      $param = $this->get_param($request,$id);
      $form = [];
      $form['status'] = 'cancel';
      $res = $this->change_status($request , $id, $form);
      return $this->save_redirect($res,$param,__('messages.task_status_cancel'));
    }

    public function progress(Request $request ,$id){
      $param = $this->get_param($request,$id);
      $form = [];
      $form['status'] = 'progress';
      $form['start_date'] = date('Y/m/d H:i:s');
      $form['end_date'] = null;
      $res = $this->change_status($request, $id, $form);
      return $this->save_redirect($res,$param, __('messages.task_status_progress'));
    }

    public function done(Request $request ,$id){
      $param = $this->get_param($request,$id);
      $form = [];
      $form['status'] = 'done';
      $form['end_date'] = date('Y/m/d H:i:s');
      $form['stars'] = null;
      $res = $this->change_status($request, $id, $form);
      return $this->save_redirect($res,$param,__('messages.task_status_done'));
    }

    public function change_status(Request $request, $id, $form){
      $param = $this->get_param($request);
      $res =  $this->transaction($request, function() use ($request, $id, $form){
        $item = $this->model()->where('id', $id)->first();
        if(isset($form['review'])){
          $this->model()->where('id',$id)->update($form['task']);
          $task = $this->model()->where('id',$id)->first();
          $task->reviews()->create($form['review']);
          $task->task_comments()->create($form['comment']);
        }else{
          $this->model()->where('id',$id)->update($form);
        }
        return $this->api_response(200, '', '', $item);
      }, __('messages.info_updated'), __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    public function show_review_page(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['_edit'] = false;
      return view($this->domain.'.review')->with($param);
    }

    public function review(Request $request, Int $id){
      $param = $this->get_param($request, $id);
      $user = $this->login_details($request);
      $form = [];

      if(!empty($request->get('review'))){
        $form['task'] = [
          'stars' => $request->get('stars'),
        ];
        $form['review'] = [
          'body' => $request->get('review'),
          'create_user_id' => $user->id,
        ];

        $form['comment'] = [
          'body' => 'レビューしました。',
          'type' => 'task',
          'create_user_id' => $user->id,
        ];
      }else {
        $form = [
          'stars' => $request->get('stars'),
        ];
      }
      $res =$this->change_status($request,$id,$form);
      return $this->save_redirect($res,$param, __('messages.task_reviewed'));
    }

    public function save_review(Request $request,$form){
      $res =  $this->transaction($request, function() use ($request, $form){
        $item = Review::create($form);
        return $this->api_response(200, '', '', $item);
      }, __('messages.info_add'), __FILE__, __FUNCTION__, __LINE__ );
      return $res;
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

    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      $ret = $this->get_common_param($request);
      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id','=',$id)->first();
        if($this->is_student($user->role) &&
          $item['target_user_id'] !== $user->user_id){
            //生徒は自分宛てのもののみ
            abort(404);
        }
        $item = $item->details();
        $ret['item'] = $item->details();
      }
      return $ret;
    }
}
