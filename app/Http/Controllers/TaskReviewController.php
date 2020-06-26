<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskReview;

class TaskReviewController extends MilestoneController
{
    //
    public $domain = 'task_reviews';
    public function model(){
      return TaskReview::query();
    }

    public function create(Request $request)
    {
        //
        $param = $this->get_param($request);
        $param['_edit'] = false;
        $param['task_id'] = $request->get('task_id');
        return view('tasks.review')->with($param);
    }

    public function _store(Request $request){
      $form = $this->create_form($request);
      $item = $this->model();

      $res = $this->transaction($request, function() use ($request, $form){
        $item = $this->model()->updateOrCreate([
          'task_id' => $form['task_id'],
          'create_user_id' => $form['create_user_id'],
        ],$form);
        return $this->api_response(200, '', '', $item);
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }

    public function create_form(Request $request){
      $form = [
        'task_id' => $request->get('task_id'),
        'evaluation' => $request->get('evaluation'),
        'create_user_id' => Auth::user()->id,
      ];

      return $form;
    }
}
