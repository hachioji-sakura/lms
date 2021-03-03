<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\User;
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
    $ret = $this->get_common_param($request);
    $ret['remind'] = false;
    $ret['token'] = false;
    $ret['is_exchange_add'] = false;
    $ret['is_proxy'] = false;
    if($request->has('is_proxy')){
      $ret['is_proxy'] = true;
    }
    if($request->has('access_key')){
      $ret['token'] = $request->get('access_key');
    }
    if($request->has('rest_reason')){
      $ret['rest_reason'] = $request->get('rest_reason');
    }
    if($request->has('cancel_reason')){
      $ret['cancel_reason'] = $request->get('cancel_reason');
    }
    if($request->has('user_calendar_setting_id')){
      $ret['user_calendar_setting_id'] = $request->get('user_calendar_setting_id');
    }
    if(is_numeric($id) && $id > 0){
      $user_id = -1;
      if($request->has('user')){
        $user_id = $request->get('user');
      }
      $item = $this->model()->where('id',$id)->first();
      if(!isset($item)){
        abort(404, 'ページがみつかりません(1)');
      }
      if($user_id>0){
        $user = User::where('id', $user_id)->first();
        if(!isset($user)){
          abort(403, '有効期限が切れています(4)');
        }
        $user = $user->details();
        $ret['user'] = $user;
      }
      if(isset($user)){
        if($this->is_manager($user->role)!=true){
          if($item->is_access($user->user_id)!=true){
            abort(403, 'このページにはアクセスできません(1)'.$user->role);
          }
        }
      }
      else {
        abort(403, 'このページにはアクセスできません(2)');
      }

      if($this->is_manager_or_teacher($user->role)){
        //講師・事務の場合、すべての生徒名を表示する(details(user_id=1)）
        $ret['item'] = $item->details(1);
      }
      else {
        //それ以外は、自分に関連するもの（親子）のみ表示する
        $ret['item'] = $item->details($user->user_id);
      }
      if($request->has('student_id') && gettype($request->get('student_id'))!='array'){
        $student = Student::where('id', $request->get('student_id'))->first();
        if(isset($student)){
          $ret['item']->own_member = $ret['item']->get_member($student->user_id);
          $ret['item']["status"] = $ret['item']->own_member->status;
          $ret['item']["status_name"] = $ret['item']->own_member->status_name();
          $ret['item']["student_name"] = $student->name();
        }
      }
    }
//dump($ret);
    return $ret;
  }
    public function examination_textbook(Request $request){
      $param = $this->get_param($request);
      $param['domain'] = "examinations";
      $_table = $this->search($request);
      return view('examinations.textbooks',   $_table)
        ->with($param);
    }

  public function index(Request $request)
  {
    $user = $this->login_details($request);

    if(!isset($user)) abort(403);
    if($this->is_manager($user->role)!=true) abort(403);

    if(!$request->has('_origin')){
      $request->merge([
        '_origin' => $this->domain,
      ]);
    }
    if(!$request->has('_line')){
      $request->merge([
        '_line' => $this->pagenation_line,
      ]);
    }
    if(!$request->has('_page')){
      $request->merge([
        '_page' => 1,
      ]);
    }
    else if($request->get('_page')==0){
      $request->merge([
        '_page' => 1,
      ]);
    }
    $sort = 'asc';
    if($request->has('is_desc') && $request->get('is_desc')==1){
      $sort = 'desc';
    }
    $request->merge([
      '_sort' => 'start_time',
      '_sort_order' => $sort,
    ]);

    $param = $this->get_param($request);
//paramのほうにfilter ありました。
    //get_common_param
    $_table = $this->search($request);



    return view($this->domain.'.lists', $_table)
      ->with($param);
  }

  public function search(Request $request)
  {
    $param = $this->get_param($request);
    $user = $this->login_details($request);
    if(!isset($user)) return $this->forbidden();
    if($this->is_manager($user->role)!=true) return $this->forbidden();
    $items = $this->model();
    $items = $this->_search_scope($request, $items);
    $items = $items->paginate($param['_line']);
//    $items = $items->orderBy($request->_sort, $request->_sort_order)->paginate($param['_line']);

    $fields = [
      'name' => [
        'label' => __('labels.textbook_name'),
        'link' => "show",
      ],
      'explain' => [
        'label' => __('labels.explain'),
      ],
      'difficulty' => [
        'label' => __('labels.difficulty'),
      ],

      'publisher_name'=> [
        "label" => __('labels.publisher_name'),
      ],
      'supplier_name' => [
        'label' => __('labels.supplier_name'),
      ],
      'subject' => [
        'label' => __('labels.subject'),
      ],
      'grade' => [
        'label' => __('labels.grade'),
      ],
      'buttons' => [
        'label' => __('labels.control'),
        'button' => [
          'edit',
          'delete']
      ]
    ];

    foreach($items as $item){
      $item->publisher_name = $item->publisher->name;
      $item->supplier_name = $item->supplier->name;
      $item->subject = $item->getSubjectName();
      $item->grade = $item->getGrade();
    }

    return ["items" => $items, "fields" => $fields];
  }

    /**
     * 検索～一覧
     *
     * @param  \Illuminate\Http\Request  $request
     * @return [Collection, field]
     */
//    public function search(Request $request)
//    {
//      $items = $this->model();
//      $user = $this->login_details($request);
//      if($this->is_manager_or_teacher($user->role)!==true){
//        //生徒の場合は所有しているものを表示する
//      }
//
//      $items = $this->_search_scope($request, $items);
//      $items = $this->_search_pagenation($request, $items);
//
//      $items = $this->_search_sort($request, $items);
//      $items = $items->get();
//      if(isset($items)){
//        foreach($items as $item){
//          $chapter = $item->chapters;
//          if(isset($item->publisher)){
//            $item->kana = '出版：'.$item->publisher->name;
//          }
//          else {
//            $item->kana = '出版：不明';
//          }
//          $icon = asset('svg/folder_in_file.svg');
//          if($item->image && !empty($item->image->s3_url)){
//            $icon = $item->image->s3_url;
//          }
//          $item->icon = $icon;
//          $item->chapter_count = count($chapter);
//        }
//      }
//      $fields = [
//        'id' => [
//          'label' => 'ID',
//        ],
//        'name' => [
//          'label' => 'タイトル',
//          'link' => 'show',
//        ],
//      ];
//      return ['items' => $items->toArray(), 'fields' => $fields];
//    }



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
