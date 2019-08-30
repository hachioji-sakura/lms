<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Textbook;
use DB;
class TextbookController extends MilestoneController
{
    public $domain = 'textbooks';
    public $table = 'textbooks';
    

    public function model(){
      return Textbook::query();
    }
    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      $ret = [
        'domain' => $this->domain,
        'domain_name' => __('labels.'.$this->domain),
        'user' => $user,
        'search_word'=>$request->search_word,
        'search_status'=>$request->status
      ];
      return $ret;
    }
    public function examination_textbook(Request $request){
      $param = $this->get_param($request);
      $param['domain'] = "examinations";
      $_table = $this->search($request);
      return view('examinations.textbooks',   $_table)
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
      $user = $this->login_details($request);
      if($this->is_manager_or_teacher($user->role)!==true){
        //生徒の場合は所有しているものを表示する
      }

      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);

      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      if(isset($items)){
        foreach($items as $item){
          $chapter = $item->chapters;
          if(isset($item->publisher)){
            $item->kana = '出版：'.$item->publisher->name;
          }
          else {
            $item->kana = '出版：不明';
          }
          $icon = asset('svg/folder_in_file.svg');
          if($item->image && !empty($item->image->s3_url)){
            $icon = $item->image->s3_url;
          }
          $item->icon = $icon;
          $item->chapter_count = count($chapter);
        }
      }
      $fields = [
        'id' => [
          'label' => 'ID',
        ],
        'name' => [
          'label' => 'タイトル',
          'link' => 'show',
        ],
      ];
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
      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        $items = $items->where(function($items)use($search_words){
          foreach($search_words as $_search_word){
            if(empty($_search_word)) continue;
            $_like = '%'.$_search_word.'%';
            $items->orWhere('name','like',$_like)->orWhere('explain','like',$_like);
          }
        });
      }

      return $items;
    }

}
