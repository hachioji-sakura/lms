<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserExamination;
use App\Models\UserAnswer;
use App\Models\TextbookQuestion;
use DB;

class UserExaminationController extends UserController
{
    private function model(){
      return UserExamination::query();
    }
    /**
     * textbook>chapterの受講状況をチェックし、問題ページを表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $textbook_id
     * @param  int  $chapter_id
     * @return view
     */
    public function examination(Request $request, $textbook_id, $chapter_id){
      $message = "";
      $user = $this->login_details();
      //受講中の状況取得
      $current_examination = $this->get_examintaion($user->user_id, $chapter_id);
      if(!isset($current_examination)){
        //受講中の状況がない=新規登録・・・？
        $res = $this->create_examination($request, $textbook_id, $chapter_id);
        if($this->is_success_responce($res)){
          $current_examination = $res["data"];
        }
        else {
          return "受講登録エラー";
          abort(500);
        }
      }

      $is_result = false;
      $question = null;
      if($this->is_result($current_examination)){
        //ステータスが終了している
        $is_result = true;
      }
      else {
        $next_question_id = $this->get_next_question_id($current_examination->id);
        if(!is_numeric($next_question_id) || $next_question_id < 1){
          //次の問題がないかステータスが終了していない
          //ステータスを終了する
          $current_examination->update(['status'=>10]);
          $is_result = true;
        }
        else {
          //次の問題あり
          $next_question = TextbookQuestion::where('id', '=', $next_question_id)->first();
          $question = $next_question->toArray();
        }
      }

      $result = null;
      if($is_result === true){
        //終了した場合、結果を設定
        $result = [
          'total' => 99,
          'score' => 100
        ];
      }
      $chapter = $current_examination->textbook_chapter;

      return view('examinations.question', [
        'textbook_id' => $textbook_id,
        'chapter_id' => $chapter_id,
        'chapter_title' => $chapter->title,
        'item' => $question,
        'result' => $result,
        'message' => $message
      ]);
    }
    /**
     * textbook>chapterの新規受講を登録し、問題ページを表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $textbook_id
     * @param  int  $chapter_id
     * @return view
     */
    public function start_examination(Request $request, $textbook_id, $chapter_id){
      $res = $this->create_examination($request, $textbook_id, $chapter_id);
      if($this->is_success_responce($res)){
        return $this->examination($request, $textbook_id, $chapter_id);
      }
      else {
        var_dump($res);
        return "受講登録エラー1";
        abort(500);
      }
    }
    /**
     * textbook>chapterの新規受講を登録
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $textbook_id
     * @param  int  $chapter_id
     * @return response
     */
    protected function create_examination(Request $request, $textbook_id, $chapter_id){
      $user = $this->login_details();
      $retry = 0;
      if(!empty($request->get('retry'))){
        $retry = 1;
      }
      //受講中の状況を取得
      $current_examination = $this->get_examintaion($user->user_id, $chapter_id);

      if(!isset($current_examination) || $this->is_result($current_examination)){
        //受講中の状況がないor 受講終了の場合、新規登録
        $parent_examination_id = 0;
        if($retry === 1 && $this->is_result($current_examination)){
          //リトライ指定があり、直近の試験が完了している場合、
          //完了している試験を親IDとする
          $miss_answer_count  = $current_examination->answers->where('judge', '=', 0)->count();
          if($miss_answer_count > 0){
            //間違えた問題がある場合（なければ新規扱い）
            $parent_examination_id = $current_examination->id;
          }
        }
        $request->merge([
          'chapter_id' => $chapter_id,
          'parent_id' => $parent_examination_id,
          'question_id' => 0,
          'status' => 0,
        ]);
        //試験登録
        $res = $this->_store($request);
        return $res;
      }
      return $this->error_responce('試験途中の状態が残っている場合は、新規登録はできません');
    }
    /**
     * 受講状況が完了している場合true
     *
     * @param  UserExamination  $examination
     * @return boolean
     */
    protected function is_result($examination){
      if($examination->status === 10){
        return true;
      }
      return false;
    }
    /**
     * 受講状況取得
     *
     * @param  int  $user_id
     * @param  int  $chapter_id
     * @return boolean
     */
    protected function get_examintaion($user_id, $chapter_id){
      return $current_examination = UserExamination::where('user_id', '=', $user_id)
        ->where('chapter_id', '=', $chapter_id)
        ->orderBy('created_at', 'desc')
        ->first();
    }
    /**
     * 対象問題の取得
     *
     * @param  int  $user_examination_id
     * @return TextbookQuestion
     */
    protected function get_questions($user_examination_id){
      //カレントの試験状態
      $current_examination = UserExamination::where('id', '=', $user_examination_id)->first();
      if($current_examination->parent_id > 0){
        //リトライ対象の問題=前回間違えたことがある問題
        $sql =<<<EOT
          select distinct q.id, q.sort_no
            from textbook_questions q
            inner join user_examinations ue on q.chapter_id = ue.chapter_id and ue.id = ?
            where q.id in (select question_id from user_answers where user_examination_id = ue.parent_id and judge = 0)
            order by q.sort_no
EOT;
        $questions = DB::select($sql, [$current_examination->id]);
      }
      else {
        //リトライではない＝章の問題
        $sql =<<<EOT
          select distinct q.id, q.sort_no
            from textbook_questions q
            inner join user_examinations ue on q.chapter_id = ue.chapter_id and ue.id = ?
            order by q.sort_no
EOT;
        $questions = DB::select($sql, [$current_examination->id]);
      }
      return $questions;
    }
    /**
     * 間違えた問題の取得
     *
     * @param  int  $user_examination_id
     * @return TextbookQuestion
     */
    protected function get_miss_questions($user_examination_id){
      //今回間違えた問題
      $sql =<<<EOT
        select distinct q.id, q.sort_no
          from textbook_questions q
          inner join user_examinations ue on q.chapter_id = ue.chapter_id and ue.id = ?
          where q.id in (select question_id from user_answers where user_examination_id = ue.id and judge = 0)
          order by q.sort_no
EOT;
      $questions = DB::select($sql, [$user_examination_id]);
      return $questions;
    }
    /**
     * 次の問題のidを取得
     *
     * @param  int  $user_examination_id
     * @return int
     */
    protected function get_next_question_id($user_examination_id){
      //カレントの試験状態
      $current_examination = UserExamination::where('id', '=', $user_examination_id)->first();
      if($current_examination->parent_id > 0){
        //リトライ対象の問題=前回間違えたことがある問題かつ、今回正解がない問題
        $sql =<<<EOT
          select distinct q.id, q.sort_no
            from textbook_questions q
            inner join user_answers ua on q.id = ua.question_id and ua.user_examination_id = ?
            where ua.judge = 0
            and q.id not in (select question_id from user_answers where user_examination_id = ? and judge = 1)
            order by q.sort_no
EOT;
        $questions = DB::select($sql, [$current_examination->parent_id, $current_examination->id]);
      }
      else {
        //リトライではない＝章の問題かつ、今回正解がない問題が対象
        $sql =<<<EOT
          select distinct q.id, q.sort_no
            from textbook_questions q
            inner join user_examinations ue on q.chapter_id = ue.chapter_id and ue.id = ?
            and q.id not in (select question_id from user_answers where user_examination_id = ue.id and judge = 1)
            order by q.sort_no
EOT;
        $questions = DB::select($sql, [$current_examination->id]);
      }
      $sort_no = -1;
      if($current_examination->current_question_id > 0){
        $current_question = TextbookQuestion::where('id', '=', $current_examination->current_question_id)->first();
        if(isset($current_question)){
          $sort_no = $current_question->sort_no;
        }
      }
      $ret = -1;
      foreach($questions as $i => $question){
        if($sort_no <= $question->sort_no){
          $ret = $question->id;
          break;
        }
      }
      return $ret;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = $this->login_details();

      //return $this->get_next_question_id(4);
      return view("examinations.sample", [
        'user' => $user
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    public function _store(Request $request)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $_item = $this->model()->create([
          'user_id' => $user->user_id,
          'parent_id' => $form['parent_id'],
          'chapter_id' => $form['chapter_id'],
          'status' => $form['status'],
          'current_question_id' => $form['question_id'],
        ]);
        DB::commit();
        return $this->api_responce(200, "", "", $_item);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
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
    public function _update(Request $request, $id)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $_item = $this->model()::find($id)->update([
          'status' => $form['status'],
          'current_question_id' => $form['question_id']
        ]);
        DB::commit();
        return $this->api_responce(200, "", "", $_item);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
          DB::rollBack();
          return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
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
