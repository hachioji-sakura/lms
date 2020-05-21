<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskComment;

class TaskCommentController extends CommentController
{
    //
    public $domain = 'task_comments';
    public function model(){
      return TaskComment::query();
    }

    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if( empty($form['body']) ){
        return $this->bad_request('リクエストエラー','内容='.$form['body']);
      }
      return $this->api_response(200, '', '');
    }

    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = [];
      $form['task_id'] = $request->get('task_id');
      $form['create_user_id'] = $user->user_id;
      $form['type'] = 'task';
      $form['body'] = $request->get('body');
      return $form;
    }
}
