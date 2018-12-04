<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAnswer;
use App\Models\UserRetryQuestion;
use App\Models\UserExamination;
use App\Models\TextbookAnswer;
use App\Models\TextbookQuestion;
use DB;
class UserAnswerController extends UserExaminationController
{
    private function model(){
      return UserAnswer::query();
    }
    /**
     * 回答判定・回答保存し、結果ページを表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $textbook_id
     * @param  int  $chapter_id
     * @return view
     */
    public function answer(Request $request, $textbook_id, $chapter_id, $question_id=null){
      $user = $this->login_details();
      $judge = 0;
      $is_save_answer = true;
      $is_traning = 0;
      $answer_text = $request->get('answer_text');

      //受講中の状況取得
      $current_examination = $this->get_examintaion($user->user_id, $chapter_id);
      if(!isset($current_examination)){
        //受講中の状況がない
        return abort(500);
      }

      //受講中の問題-直近の回答を取得
      $current_answer = $current_examination->answers->where('question_id','=', $question_id)
                          ->sortByDesc('created_at')->first();
      if(isset($current_answer)){
        //直近の回答が間違いの場合、練習モードとする
        if($current_answer->judge===0){
          $is_traning = 1;
        }
        //無効回答：画面再表示などで再POSTした
        if($current_answer->start_time === $request->get('start_time')){
          $is_save_answer = false;
        }
      }

      //問題・回答を取得
      $question = TextbookQuestion::where('chapter_id', '=', $chapter_id)
          ->where('id', '=', $question_id)->first();
      $chapter = $question->chapter;
      $chapter_title = $chapter->title;

      $question->textbook_id = $chapter->textbook_id;

      $question_answers = $question->answers;
      //回答判定
      if(!empty($answer_text)){
        foreach($question_answers as $question_answer){
          if(empty($question->answer_text)) $question->answer_text = $question_answer->answer_text;
          if($question_answer->answer_text===trim($answer_text)){
            //正解
            $judge = 1;
          }
        }
      }
      else {
        $answer_text = '';
      }

      //回答保存
      if($is_save_answer===true){
        $request->merge([
          'question_id' => $question_id,
          'user_examination_id' => $current_examination->id,
          'judge' => $judge,
          'answer_text' => $answer_text,
          'is_traning' => $is_traning,
          'score' => $question->score
        ]);
        $res = $this->save_answer($request);
        if(!$this->is_success_responce($res)){
          //abort(500);
          return $res;
        }
      }

      $next_question_id = $this->get_next_question_id($current_examination->id);
      $next_question = TextbookQuestion::where('id', '=', $next_question_id)->first();
      $result = null;
      if(!isset($next_question)){
        //章問題についてすべて終了した場合、結果を設定
        $_total = count($this->get_questions($current_examination->id));
        $_success = $_total - count($this->get_miss_questions($current_examination->id));
        $result = [
          'total' => $_total,
          'success' => $_success
        ];
      }

      return view('examinations.result',[
        'textbook_id' => $textbook_id,
        'chapter_id' => $chapter_id,
        'chapter_title' => $chapter->title,
        'item' => $question->toArray(),
        'judge'=>$judge,
        'is_traning' => $is_traning,
        'result'=>$result,
        'answer_text'=>$answer_text
      ]);
    }
    /**
     * 回答を保存する（採点も行う）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return response
     */
    public function save_answer(Request $request)
    {
      try {
        DB::beginTransaction();
        $form = $request->all();
        $user = $this->login_details();

        if($form['judge']===0){
          //間違え
          $next_question_id = $form['question_id'];
          $form['score'] = 0;
        }

        if($form['is_traning']===1){
          $form['score'] = 0;
        }
        unset($form['next_question_id']);
        unset($form['_token']);
        $_item = $this->model()->create($form);

        $next_question_id = $this->get_next_question_id($form['user_examination_id']);
        $status = 1;
        if($next_question_id < 1){
          //次の問題がなければ終了
          $status = 10;
        }
        $_item = UserExamination::find($form['user_examination_id'])->update([
          'status' => $status,
          'current_question_id' => $next_question_id
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
    public function _store(Request $request)
    {
      $form = $request->all();
      try {
        DB::beginTransaction();
        $user = $this->login_details();
        $_item = $this->model()->create($form);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
