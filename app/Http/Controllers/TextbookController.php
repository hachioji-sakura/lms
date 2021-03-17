<?php

namespace App\Http\Controllers;

use App\Models\GeneralAttribute;
use App\Models\Publisher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Supplier;
use App\Models\TextbookSubject;
use App\Models\TextbookTag;
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
   * テキスト新規登録画面
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $param = $this->get_param($request);
    return view($this->domain.'.create',
      [ 'error_message' => '', '_edit' => false])
      ->with($param);
  }

  /**
   * テキスト新規登録
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $param = $this->get_param($request);
    $res = $this->_store($request);
    if(empty($res['message'])){
      $message = '登録しました。';
    }else{
      $message = $res['message'];
    }
    return $this->save_redirect($res, $param, $message);
  }

  /**
   * 新規登録ロジック
   *
   * @return \Illuminate\Http\Response
   */
  public function _store(Request $request)
  {
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    return $this->transaction($request, function() use ($request){
      $user = $this->login_details($request);
      $form = $request->all();
      $form['create_user_id'] = $user->user_id;
      $item = new Textbook();
      $item->store_textbook($form);

      return $this->api_response(200, '', '', $item);
    }, '情報更新', __FILE__, __FUNCTION__, __LINE__ );
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
//    $sort = 'asc';
//    if($request->has('is_desc') && $request->get('is_desc')==1){
//      $sort = 'desc';
//    }
//    $request->merge([
//      '_sort' => 'start_time',
//      '_sort_order' => $sort,
//    ]);

    $param = $this->get_param($request);
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
    //検索条件
    $items = $this->_search_scope($request, $items);
    $items = $items->paginate($param['_line']);

    $fields = [
      'name' => [
        'label' => __('labels.textbook_name'),
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
      $item->difficulty = config('attribute.difficulty')[$item->difficulty]??'';

      $subjects = $item->get_subjects();
      $subject_names = '';
      $item->subject= '';
      if(!empty($subjects) && $subjects!==[] ){
        foreach($subjects as $subject) {
          $subject_names = $subject_names . $subject->name . ',';
        }
        $item->subject = mb_substr($subject_names, 0, -1);
      }

      $grades = $item->get_grades();
      $grade_names = '';
      if(isset($grades)){
        foreach($grades as $grade){
          $grade_names = $grade_names . $grade->attribute_name . ',';
        }
        $item->grade = mb_substr($grade_names, 0, -1);
      }
    }
    return ["items" => $items, "fields" => $fields];
  }

  /**
   * フィルタリングロジック
   *
   * @param Request $request
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

    $forms = $request->all();
    $scopes = ['publisher_id','supplier_id','difficulty'];
    foreach($scopes as $scope){
      if(isset($forms[$scope])){
        $items = $items->where($scope,$forms[$scope]);
      }
    }

    if(isset($forms['subject'])){
      foreach($forms['subject'] as $subject){
        $items = $items->whereHas('textbook_subject', function($q) use ($subject){
          $q->where('subject_id',$subject );
        });
      }
    }

    if(isset($forms['grade_no'])){
      foreach($forms['grade_no'] as $grade_no){
        $items = $items->whereHas('textbook_tag', function($q) use ($grade_no){
          $q->where('tag_value',$grade_no );
        });
      }
    }

    $grades = GeneralAttribute::findKey('grade')->get();
    return $items;
  }

  /**
   * テキスト編集画面表示.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $textbook = Textbook::where('id', $id)->first();
    if(isset($textbook)) {
      $param = $this->get_param($request, $id);
      $param['textbook'] = $textbook;

      $textbook_prices = $textbook->get_prices();
      $param['textbook_prices']=[];
      if(!empty($textbook_prices)) {
        foreach ($textbook_prices as $textbook_price) {
          $param['textbook_prices'][$textbook_price->tag_key] = $textbook_price->tag_value;
        }
      }

      $param['textbook_subjects']=[];
      foreach($textbook->textbook_subject as $textbookSubject){
        $param['textbook_subjects'][] = $textbookSubject->subject->name;
      }

      $textbook_grades = $textbook->get_grades();
      $param['textbook_grades']=[];
        if(!empty($textbook_grades)) {
        foreach($textbook_grades as $textbookGrade) {
          $param['textbook_grades'][] = $textbookGrade->attribute_name;
        }
      }
    }else{
      abort('404');
    }
    return view($this->domain.'.create', [
      '_edit' => true])
      ->with($param);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param Request $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $res = $this->_update($request, $id);

    return $this->save_redirect($res, $param, '更新しました。');
  }

  public function _update(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }

    return $this->transaction($request, function() use ($request, $id){
      $user = $this->login_details($request);
      $form = $request->all();
      $form['create_user_id'] = $user->user_id;
      $item = $this->model()->where('id',$id)->first();
      $item->update_textbook($form);

      return $this->api_response(200, '', '', $item);
    }, $param['domain_name'].'情報更新', __FILE__, __FUNCTION__, __LINE__ );
  }

  /**
   * 詳細画面表示
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $fields = $this->show_fields($param['item']->type);
    $form = $request->all();
    $form['fields'] = $fields;

    $item = Textbook::find($id);
    $item->publisher_name = $item->publisher->name;
    $item->supplier_name = $item->supplier->name;
    $item->difficulty = config('attribute.difficulty')[$item->difficulty]??'';

    $subjects = $item->get_subjects();
    $subject_names = '';
    if(isset($subjects)){
      foreach($subjects as $subject) {
        $subject_names = $subject_names . $subject->name . ',';
      }
      $item->subject = mb_substr($subject_names, 0, -1);
    }

    $grades = $item->get_grades();
    $grade_names = '';
    if(isset($grades)){
      foreach($grades as $grade){
        $grade_names = $grade_names . $grade->attribute_name . ',';
      }
      $item->grade = mb_substr($grade_names, 0, -1);
    }

    $param['item'] =$item;

    if($request->has('api')) {
      return $this->api_response(200, '', '', $param['item']);
    }
    return view('textbooks.page', $form)
      ->with($param);
  }

  /**
   * 詳細画面表示のデータ取得
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show_fields($type=''){
    $fields = [
      'name' => [
        'label' => __('labels.textbook_name'),
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
    ];

    return $fields;
  }


  /**
   * テキスト削除
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $param = $this->get_param($request, $id);

    $res = $this->_delete($request, $id);

    return $this->save_redirect($res, $param, '削除が完了しました');
  }

  public function _delete(Request $request, $id)
  {
    $form = $request->all();
    $res = $this->transaction($request, function() use ($request, $form, $id){
      $item = $this->model()->where('id',$id)->first();
      $item->dispose();
      return $this->api_response(200, '', '', $item);
    }, '依頼を取り消しました', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }




  /**
   * 共通パラメータ取得
   *
   * @param Request $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null){
    //User取得
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
    if(isset($id)){
      $ret['item']['textbook'] = Textbook::find($id);
      $ret['item']['publisher'] = $ret['item']['textbook']->publisher;
    }
    $ret['item']['publishers'] = Publisher::get();
    $ret['item']['suppliers'] = Supplier::get();
    $ret['item']['subjects'] = Subject::get();
    $ret['item']['grades'] = GeneralAttribute::findKey('grade')->get();
    return $ret;
  }

  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    //保存時にパラメータをチェック
    return $this->api_response(200, '', '');
  }
}
