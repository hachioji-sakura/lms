<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends MilestoneController
{
  public $domain = 'faqs';
  public $table = 'faqs';
  public function model(){
    return Faq::query();
  }
  public function index(Request $request)
  {
    if(!$request->has('_origin')){
      $request->merge([
        '_origin' => $this->domain,
      ]);
    }
    $param = $this->get_param($request);
    $_table = $this->search($request);
    return view($this->domain.'.lists', $_table)
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
    $items = $this->_search_scope($request, $items);
    //$items = $this->_search_pagenation($request, $items);
    if(!(isset($user) && $this->is_manager($user->role))){
      $items = $items->where('publiced_at' , '<=', date('Y-m-d'));
    }
    if((isset($user) && $this->is_manager_or_teacher($user->role))){
      $items = $items->findTypes(['teacher','manager'], true);
    }
    $items = $this->_search_sort($request, $items);
    $count = $items->count();
    $items = $items->get();
    foreach($items as $item){
      $create_user = $item->create_user->details();
      $item->create_user_name = $create_user->name;
      unset($item->create_user);
    }
    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'title' => [
        'label' => 'タイトル',
        'link' => 'show',
      ],
    ];
    $fields['create_user_name'] = [
      'label' => '起票者',
    ];
    $fields['publiced_at'] = [
      'label' => '公開日',
    ];
    $fields['created_at'] = [
      'label' => __('labels.add_datetime'),
    ];
    $fields['buttons'] = [
      'label' => '操作',
      'button' => ['edit', 'delete']
    ];
    return ['items' => $items, 'fields' => $fields, 'count' => $count];
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_type'=>$request->search_type,
    ];

    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      $create_user = $item->create_user->details();
      $item->create_user_name = $create_user->name;
      unset($item->create_user);
      $item->_type_name = $item->type_name();

      $ret['item'] = $item;
    }
    return $ret;
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
    //type 検索
    if(isset($request->search_type) && !empty($request->search_type)){
      $_param = "";
      if(gettype($request->search_type) == "array") $_param  = $request->$search_type;
      else $_param = explode(',', $request->search_type.',');
      $items = $items->findTypes($_param);
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
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['publiced_at'] = '9999-12-31';
    if($this->is_manager_or_teacher($user->role)){
      $form['publiced_at'] = date('Y-m-d');
    }
    $form['create_user_id'] = $user->user_id;
    $form['type'] = $request->get('type');
    $form['title'] = $request->get('title');
    $form['body'] = $request->get('body');
    return $form;
  }
  public function update_form(Request $request){
    $form = [];
    if(!empty($request->get('publiced_at'))){
      $form['publiced_at'] = $request->get('publiced_at');
    }
    $form['type'] = $request->get('type');
    $form['title'] = $request->get('title');
    $form['body'] = $request->get('body');
    return $form;
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
    $fields = [
      '_type_name' => [
        'label' => '種別',
      ],
      'title' => [
        'label' => 'タイトル',
      ],
      'body' => [
        'label' => '内容',
      ],
    ];
    $fields['publiced_at'] = [
      'label' => '公開日',
    ];
    return view('components.page', [
      'fields'=>$fields])
      ->with($param);
  }
  public function page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $fields = [
      'body' => [
        'label' => '→',
      ],
    ];
/*
    $fields = [
      '_type_name' => [
        'label' => '種別',
      ],
      'title' => [
        'label' => 'タイトル',
      ],
      'body' => [
        'label' => '内容',
      ],
    ];
    //生徒以外の場合は、対象者も表示する
    $fields['publiced_at'] = [
      'label' => '公開日',
    ];
*/
    return view('faqs.page', [
      'fields'=>$fields])
      ->with($param);
  }

  /**
   * コメント公開用ページ
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function publiced_page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    if(!$this->is_manager($param['user']->role)){
      //事務以外アクセス不可
      abort(403);
    }

    $fields = [
      '_type_name' => [
        'label' => '種別',
      ],
      'title' => [
        'label' => 'タイトル',
      ],
      'body' => [
        'label' => '内容',
      ],
    ];
    $fields['create_user_name'] = [
      'label' => '起票者',
    ];
    $fields['publiced_at'] = [
      'label' => '公開日',
    ];

    return view('faqs.publiced', [
      'fields'=>$fields])
      ->with($param);
  }


}
