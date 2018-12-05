<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use DB;
class CommentController extends MilestoneController
{
    public $domain = 'comments';
    public $table = 'comments';
    public $domain_name = 'コメント';
    public function model(){
      return Comment::query();
    }
    public function create_form(Request $request){
      $user = $this->login_details();
      $form = [];
      $form['publiced_at'] = '9999-12-31';
      $form['create_user_id'] = $user->user_id;
      $form['type'] = $request->get('type');
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      if($this->is_student($user->role)===true){
        //生徒の場合は自分自身を対象とする
        $form['target_user_id'] = $user->user_id;
      }
      else {
        if($request->has('student_id')){
          $u = Student::find($request->get('student_id'));
        }
        else if($request->has('teacher_id')){
          $u = Teacher::find($request->get('teacher_id'));
        }
        else if($request->has('manager_id')){
          $u = Manager::find($request->get('manager_id'));
        }
        $form['target_user_id'] = $u->user_id;
      }
      return $form;
    }
    public function update_form(Request $request){
      $form = [];
      if(!empty($request->get('publiced_at'))){
        $form['publiced_at'] = $request->get('publiced_at');
      }
      $form['type'] = $request->get('type');
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      return $form;
    }

}
