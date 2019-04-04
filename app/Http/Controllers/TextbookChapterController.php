<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Textbook;
use App\Models\TextbookChapter;
use App\Models\TextbookQuestion;
use App\Models\UserExamination;

use DB;
class TextbookChapterController extends TextbookController
{
    public $domain = 'textbook_chapters';
    public $table = 'textbook_chapters';
    public $domain_name = '目次';

    public function model(){
      return TextbookChapter::query();
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $textbook_id=null){
      $user = $this->login_details();
      if(!is_numeric($textbook_id) || $textbook_id <= 0){
        abort(500);
      }
      $textbook = Textbook::where('id',$textbook_id);
      if(!isset($textbook)){
        abort(404);
      }
      $textbook = $textbook->first();
      $ret = [
        'domain' => $this->domain,
        'domain_name' => $this->domain_name,
        'textbook_title' => $textbook->name,
        'user' => $user,
        'search_word'=>$request->search_word,
        'search_status'=>$request->status
      ];
      return $ret;
    }

    public function examination_chapter(Request $request, $textbook_id){
      $param = $this->get_param($request, $textbook_id);
      $param['domain'] = "examinations";
      if(!$request->has('textbook_id')){
        $request->merge([
          'textbook_id' => $textbook_id,
        ]);
      }
      $_table = $this->search($request);
      return view('examinations.chapters',   $_table)
        ->with($param);
    }
    /**
     * 検索～一覧
     *
     * @param  \Illuminate\Http\Request  $request
     * @return [Collection, field]
     */
    public function search(Request $request)
    {
      $items = $this->model();
      $user = $this->login_details();
      if($this->is_manager_or_teacher($user->role)!==true){
        //生徒の場合は所有しているものを表示する
      }

      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);

      if(!$request->has('_sort')){
        $request->merge([
          '_sort' => 'sort_no',
        ]);
      }

      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      $fields = [
        'id' => [
          'label' => 'ID',
        ],
        'title' => [
          'label' => 'タイトル',
          'link' => 'show',
        ],
      ];

      foreach($items as $item){
        $questions = $item->questions;
        $item->question_count = count($questions);
        $item->examination_status = 0;
        $item->examination_count = count($this->get_examintaions($user->user_id, $item->id));
        $_examination = $this->get_examintaion($user->user_id, $item->id);
        if(isset($_examination)){
          $item->examination_status = $_examination->status;
          //章問題についてすべて終了した場合、結果を設定
          $item->question_count = count($this->get_questions($_examination->id));
          //$item->examination_success = $item->question_count - count($this->get_miss_questions($_examination->id));
        }
      }
      return ['items' => $items->toArray(), 'fields' => $fields];
    }
    /**
     * フィルタリングロジック
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Collection $items
     * @return Collection
     */
    public function _search_scope(Request $request, $items)
    {
      //ID 検索
      if(isset($request->id)){
        $items = $items->where('id',$request->id);
      }
      //textbook_id 検索
      if(isset($request->textbook_id)){
        $items = $items->where('textbook_id',$request->textbook_id);
      }
      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        $items = $items->where(function($items)use($search_words){
          foreach($search_words as $_search_word){
            if(empty($_search_word)) continue;
            $_like = '%'.$_search_word.'%';
            $items->orWhere('title','like',$_like)->orWhere('body','like',$_like);
          }
        });
      }

      return $items;
    }
    /**
     * 受講状況取得
     *
     * @param  int  $user_id
     * @param  int  $chapter_id
     * @return boolean
     */
    protected function get_examintaions($user_id, $chapter_id){
      return $current_examination = UserExamination::where('user_id',$user_id)
        ->where('chapter_id',$chapter_id)
        ->orderBy('created_at', 'desc')
        ->get();
    }
    /**
     * 受講状況取得
     *
     * @param  int  $user_id
     * @param  int  $chapter_id
     * @return boolean
     */
    protected function get_examintaion($user_id, $chapter_id){
      return $current_examination = UserExamination::where('user_id',$user_id)
        ->where('chapter_id',$chapter_id)
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
      $current_examination = UserExamination::where('id',$user_examination_id)->first();
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
      $current_examination = UserExamination::where('id',$user_examination_id)->first();
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
        $current_question = TextbookQuestion::where('id',$current_examination->current_question_id)->first();
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

}
