<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Comment;
use App\Models\Student;
use Illuminate\Http\Request;
use DB;
class CommentController extends StudentController
{
  public function index(Request $request)
  {
    /*
    $items = $this->comments($request);
    return $items;
    */
    $comments = User::find(3)->target_comments;
    $comments = $comments->sortByDesc('created_date');
    foreach($comments as $comment){
      $create_user = $comment->create_user->getData();
      $comment->create_user_name = $create_user->name;
      $comment->create_user_kana = $create_user->kana;
      $comment->create_user_icon = $create_user->icon;
      unset($comment->create_user);
    }
    return $comments->toArray();
  }
  public function student_comments()
  {

  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function student_comments_store(Request $request, $id)
  {
    $user = $this->get_login_user();
    $form = $request->all();
    try {
      DB::beginTransaction();
      $Comment = new Comment;
      $form['create_user_id'] = $user->user_id;
      $form['publiced_at'] = date("Y/m/d");
      if($this->is_student($user->role)===true){
        //生徒の場合は自分自身を対象とし、コメントは公開しない
        $form['publiced_at'] = '9999-12-31';
        $form['target_user_id'] = $user->user_id;
      }
      else {
        $student = Student::find($id);
        $form['target_user_id'] = $student->user_id;
      }
      unset($form['_token']);
      $Comment->fill($form)->save();
      DB::commit();
    }
    catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        abort(500, $e->getMessage());
    }
    catch(\Exception $e){
        DB::rollBack();
        return back()->with('error_message','登録に失敗しました。');
    }
    return back()->with('success_message','コメントを追加しました');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}
