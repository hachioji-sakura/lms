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
    public $domain = 'user_answers';
    public $table = 'user_answers';


    public function model(){
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
      $judge = 0;
      $is_save_answer = true;
      $is_traning = 0;
      $answer_text = $request->get('answer_text');

      //受講中の状況取得
      $param = $this->get_param($request, $chapter_id);
      $current_examination = $param['current_examination'];

      //受講中の問題-直近の回答を取得
      $current_answer = $current_examination->answers->where('question_id',$question_id)
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
      $question = TextbookQuestion::where('chapter_id',$chapter_id)
          ->where('id',$question_id)->first();
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
        if(!$this->is_success_response($res)){
          //abort(500);
          return $res;
        }
      }

      $result = $this->get_result($current_examination->id);
      $chapter = $current_examination->textbook_chapter;
      $textbook = $chapter->textbook;

      return view('examinations.result',[
        'textbook_id' => $textbook_id,
        'chapter_id' => $chapter_id,
        'textbook_title' => $textbook->name,
        'chapter_title' => $chapter->title,
        'item' => $question->toArray(),
        'judge'=>$judge,
        'is_traning' => $is_traning,
        'result'=>$result,
        'answer_text'=>$answer_text
      ])
      ->with($param);
    }
    private function answer_judge($input, $answer){

    }
    /**
     * 回答を保存する（採点も行う）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return response
     */
    public function save_answer(Request $request)
    {
      return $this->transaction($request, function() use ($request){
        $form = $request->all();
        $user = $this->login_details($request);

        if($form['judge']===0){
          //間違え
          $next_question_id = $form['question_id'];
          $form['score'] = 0;
        }

        if($form['is_traning']===1){
          $form['score'] = 0;
        }
        $_item = $this->model()->create([
          'start_time' => $form['start_time'],
          'answer_text' => $form['answer_text'],
          'question_id' => $form['question_id'],
          'user_examination_id' => $form['user_examination_id'],
          'judge' => $form['judge'],
          'is_traning' => $form['is_traning'],
          'score' => $form['score'],
        ]);

        $next_question_id = $this->get_next_question_id($form['user_examination_id']);
        $status = 1;
        if($next_question_id < 1){
          //次の問題がなければ終了
          $status = 10;
        }
        $item = UserExamination::where('id',$form['user_examination_id'])->update([
          'status' => $status,
          'current_question_id' => $next_question_id
        ]);

        return $item;
      }, '回答保存', __FILE__, __FUNCTION__, __LINE__ );

    }
    public function _store(Request $request)
    {
      return $this->transaction($request, function() use ($request){
        $form = $request->all();

        $user = $this->login_details($request);

        $_item = $this->model()->create([
          'start_time' => $form['start_time'],
          'answer_text' => $form['answer_text'],
          'question_id' => $form['question_id'],
          'user_examination_id' => $form['user_examination_id'],
          'judge' => $form['judge'],
          'is_traning' => $form['is_traning'],
          'score' => $form['score'],
        ]);
        return $_item;
      }, '回答登録', __FILE__, __FUNCTION__, __LINE__ );
    }
}
