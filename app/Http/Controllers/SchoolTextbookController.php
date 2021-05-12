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
    $school_id = $request->get('schools_id') ? $request->get('schools_id') : $request->get('school_id');
    $param = $this->get_param($request,null,$school_id);

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
    $school_id = $request->get('school_id');
    $param = $this->get_param($request,null,$school_id);
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
    return view($this->domain.'.page', $form)
      ->with($param);
  }

  public function search(Request $request)
  {
    $user = $this->login_details($request);
    if(!isset($user)) return $this->forbidden();
    if($this->is_manager($user->role)!=true) return $this->forbidden();

    $school_id = $request->get('schools_id') ? $request->get('schools_id') : $request->get('school_id');
    $param = $this->get_param($request,null,$school_id);
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
      'buttons' => [
        'label' => __('labels.control'),
        'button' => [
          'delete'
        ]
      ]
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

  public function _delete(Request $request, $id)
  {
    $form = $request->all();

    $res = $this->transaction($request, function() use ($request, $form, $id){
      $item = $this->model()->find($id);
      $item->dispose();
      return $this->api_response(200, '', '', $item);
    }, '依頼を取り消しました', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }

  /**
   * 共通パラメータ取得
   *
   * @param Request $request
   * @param  $textbook_id
   * @param  $school_id
   * @return json
   */
  public function get_param(Request $request, $textbook_id = null, $school_id = null){

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

    if(!empty($school_id)){
      $user_id = -1;

      if($request->has('user')){
        $user_id = $request->get('user');
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
          if($user->is_access($user->user_id)!=true){
            abort(403, 'このページにはアクセスできません(1)'.$user->role);
          }
        }
      }
      else {
        abort(403, 'このページにはアクセスできません(2)');
      }

      $ret['school'] = School::find($school_id);
      $ret['school_id'] = $school_id;
      $ret['school_textbooks'] = $ret['school']->textbooks();

    }
    if(!empty($textbook_id)){
      $ret['item'] = $this->model()->find($textbook_id);
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
