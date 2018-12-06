<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Textbook;
use App\Models\TextbookChapter;
use App\Models\TextbookQuestion;
use App\Models\UserExamination;
use App\Models\UserAnswer;
use DB;

class UserExaminationController extends TextbookChapterController
{
    public $domain = 'user_examinations';
    public $table = 'user_examinations';
    public $domain_name = '問題';

    public function model(){
      return UserExamination::query();
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $chapter_id=null){
      $user = $this->login_details();
      if(!is_numeric($chapter_id) || $chapter_id <= 0){
        abort(500);
      }
      //受講中の状況取得
      $current_examination = $this->get_examintaion($user->user_id, $chapter_id);
      $ret = [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        'current_examination' => $current_examination,
        'user' => $user,
        'search_word'=>$request->search_word,
        'search_status'=>$request->status
      ];
      return $ret;
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

      //受講中の状況取得
      $_param = $this->get_param($request, $chapter_id);
      $current_examination = $_param['current_examination'];

      if(!isset($current_examination)){
        //受講中の状況がない=新規登録・・・？
        $res = $this->create_examination($request, $textbook_id, $chapter_id);
        if($this->is_success_responce($res)){
          $current_examination = $res["data"];
        }
        else {
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

      $result = $this->get_result($current_examination->id);
      $chapter = $current_examination->textbook_chapter;
      $textbook = $chapter->textbook;
      return view('examinations.question', [
        'textbook_id' => $textbook_id,
        'chapter_id' => $chapter_id,
        'textbook_title' => $textbook->name,
        'chapter_title' => $chapter->title,
        'item' => $question,
        'result' => $result,
      ])
      ->with($_param);
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
    protected function get_result($user_examination_id){
      $next_question_id = $this->get_next_question_id($user_examination_id);
      $next_question = TextbookQuestion::where('id', '=', $next_question_id)->first();
      $result = null;
      if(!isset($next_question)){
        //章問題についてすべて終了した場合、結果を設定
        $_total = count($this->get_questions($user_examination_id));
        $_success = $_total - count($this->get_miss_questions($user_examination_id));
        $result = [
          'total' => $_total,
          'success' => $_success
        ];
      }
      return $result;
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
      $retry = 0;
      if(!empty($request->get('retry'))){
        $retry = 1;
      }
      //受講中の状況を取得
      $_param = $this->get_param($request, $chapter_id);
      $current_examination = $_param['current_examination'];

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
      $user = $this->login_details();

      //return $this->get_next_question_id(4);
      return view("examinations.sample", [
        'user' => $user
      ]);
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

}
