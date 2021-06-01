<?php

namespace App\Http\Controllers;

use App\Models\GeneralAttribute;
use App\Models\Subject;
use App\Models\School;
use App\User;
use Illuminate\Http\Request;
use App\Models\Textbook;
class SchoolTextbookController extends MilestoneController
{
  public $domain = 'school_textbooks';
  public $table = 'school_textbooks';

  public function model(){
    return Textbook::query();
  }

  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / school_textbooks.lists
   */
  public function index(Request $request)
  {
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

    $param = $this->get_param($request);
    $user = $param['user'];
    if(!$this->is_manager($user->role)){
      //事務以外 一覧表示は不可能
      abort(403);
    }

    $_table = $this->search($request);

    return view($this->domain.'.lists', $_table)
      ->with($param);
  }

  /**
   * 新規登録画面
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
      $form = $this->create_form($request);
      $item = School::find($form['school_id']);
      $item->store_school_textbooks($form);
      return $this->api_response(200, '', '', $item);
    }, '情報更新', __FILE__, __FUNCTION__, __LINE__ );
  }

  /**
   * 詳細画面表示
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request,$id);

    $fields = $this->show_fields($param['item']->type);
    if($this->is_manager_or_teacher($param['user']->role)===true){
      //生徒以外の場合は、対象者も表示する
      if(isset($param['item']['target_user_id'])){
        $fields['target_user_name'] = [
          'label' => 'ユーザー',
        ];
      }
    }

    $form = $request->all();
    $form['fields'] = $fields;
    if($request->has('api')) return $this->api_response(200, '', '', $param['item']);
    return view('components.page', $form)
      ->with($param);
  }

  public function search(Request $request)
  {
    $user = $this->login_details($request);
    if(!isset($user)) return $this->forbidden();
    if($this->is_manager($user->role)!=true) return $this->forbidden();

    $param = $this->get_param($request);
    //検索条件
    $items = $this->_search_scope($request, $param['school_textbooks']);
    $items = $items->paginate($param['_line']);

    $fields = [
      'name' => [
        'label' => __('labels.textbook_name'),
        'link' => 'show',
      ],
      'subject_list' => [
        'label' => __('labels.subject'),
      ],
      'grade_list' => [
        'label' => __('labels.grade'),
      ],
      'difficulty_name' => [
        'label' => __('labels.difficulty'),
      ],
    ];

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
    $forms = $request->all();
    $prefix = 'search_';
    //ID 検索

    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      $items = $items->searchWord($search_words);
    }
    if(isset($request->search_keyword)){
      $search_keyword = explode(' ', $request->search_keyword);
      $items = $items->searchWord($search_keyword);
    }

    if(isset($forms['search_subject'])){
      $items = $items->searchSubject($forms['search_subject']);
    }

    if(isset($forms['search_grade'])){
      $items = $items->searchGrade($forms['search_grade']);
    }

    return $items;
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
      'subject_list' => [
        'label' => __('labels.subject'),
      ],
      'grade_list' => [
        'label' => __('labels.grade'),
      ],
      'difficulty_name' => [
        'label' => __('labels.difficulty'),
      ],
    ];

    return $fields;
  }

  /**
   * 共通パラメータ取得
   *
   * @param Request $request
   * @param  $id
   * @param  $school_id
   * @return json
   */
  public function get_param(Request $request, $id = null){

    $user = $this->login_details($request);
    if(isset($user)){
      if($this->is_manager($user->role)!=true){
          if($user->is_access($user->user_id)!=true){
              abort(403, 'このページにはアクセスできません(1)'.$user->role);
          }
      }
    }else{
      abort(403, 'このページにはアクセスできません(2)');
    }

    $ret = $this->get_common_param($request);
    $school_id = 0;
    if($request->has('school_id')) $school_id = $request->school_id;

    if(!empty($school_id)){
      $ret['school'] = School::find($school_id);
      if(!isset($ret['school'])){
        abort(404, 'ページがみつかりません(1)');
      }
      $ret['school_id'] = $school_id;
      $ret['school_textbooks'] = $ret['school']->textbooks();
    } elseif (empty($id) && empty($school_id)){
        abort(400);
    }

    if(!empty($id)){
      $ret['item'] = $this->model()->find($id);
      if(!isset($ret['item'])){
        abort(404, 'ページがみつかりません(1)');
      }
    }
    $ret['textbooks'] = Textbook::get();
    $ret['subjects'] = Subject::get();
    $ret['grades'] = GeneralAttribute::findKey('grade')->get();

    return $ret;
  }

  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form['create_user_id'] = $user->user_id;
    $form['school_id'] = $request->get('school_id');
    $form['textbooks'] = $request->get('textbooks');
    return $form;
  }

  /**
   * データ更新時のパラメータチェック
   *
   * @return \Illuminate\Http\Response
   */
  public function save_validate(Request $request)
  {
    $form = $request->all();
    if(empty($form['school_id'])){
      return $this->bad_request('リクエストエラー');
    }
    return $this->api_response(200, '', '');
  }
}
