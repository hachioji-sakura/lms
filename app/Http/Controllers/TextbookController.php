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
      $item = new Textbook();
      $item->store_textbook($form);
      return $this->api_response(200, '', '', $item);
    }, '情報更新', __FILE__, __FUNCTION__, __LINE__ );
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
      'subject_list' => [
        'label' => __('labels.subject'),
      ],
      'grade_list' => [
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
      $item->difficulty = config('attribute.difficulty')[$item->difficulty]??'';
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
    $param = $this->get_param($request, $id);
    $textbook =  $param['item']['textbook'];
    if(isset($textbook)) {
      $param['textbook'] = $textbook;
      $param['textbook_prices'] = $textbook->prices;
      $param['textbook_subjects'] = $textbook->subject_list;
      $param['textbook_grades'] =$textbook->grade_list;
    }else{
      abort('404');
    }
    return view($this->domain.'.create', [
      '_edit' => true])
      ->with($param);
  }

  public function _update(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }

    return $this->transaction($request, function() use ($request, $param){
      $user = $this->login_details($request);
      $form = $this->create_form($request);
      $form['create_user_id'] = $user->user_id;
      $param['item']['textbook']->update_textbook($form);
      return $this->api_response(200, '', '', $param['item']);
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
    $param['item']= Textbook::find($id);
    $param['item']->difficulty = config('attribute.difficulty')[$param['item']->difficulty]??'';

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
      'subject_list' => [
        'label' => __('labels.subject'),
      ],
      'grade_list' => [
        'label' => __('labels.grade'),
      ],
    ];

    return $fields;
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
      $ret['item'] = $this->model()->where('id',$id)->first();

      if(!isset( $ret['item'])){
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
          if($user->is_access($user->user_id)!=true){
            abort(403, 'このページにはアクセスできません(1)'.$user->role);
          }
        }
      }
      else {
        abort(403, 'このページにはアクセスできません(2)');
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

  public function create_form(Request $request)
  {
    $user = $this->login_details($request);
    $form['name'] = $request->get('name');
    $form['explain'] = $request->get('explain')??'';
    $form['difficulty'] = $request->get('difficulty')??0;
    $form['publisher_id'] = $request->get('publisher_id');
    $form['supplier_id'] = $request->get('supplier_id');
    $form['subjects'] = $request->get('subject')??[];
    $form['grade_no'] = $request->get('grade_no')??[];
    $form['teika_price'] = $request->get('teika_price');
    $form['selling_price'] = $request->get('selling_price');
    $form['amazon_price'] = $request->get('amazon_price');
    $form['publisher_price'] = $request->get('publisher_price');
    $form['other_price'] = $request->get('other_price');
    $form['create_user_id'] = $user->id;
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
    if(empty($form['name'])){
      return $this->bad_request('リクエストエラー', '名前='.$form['name']);
    }
    return $this->api_response(200, '', '');
  }
}
