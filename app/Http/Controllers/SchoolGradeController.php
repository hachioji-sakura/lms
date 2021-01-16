<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolGrade;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use DB;

class SchoolGradeController extends MilestoneController
{
  public $domain = 'school_grades';
  public $table = 'school_grades';
  public function model(){
    return SchoolGrade::query();
  }
  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request)
  {
    $param = $this->get_param($request);
    $items = $this->model();
    $user = $this->login_details($request);
    if($this->is_manager_or_teacher($user->role)!==true){
      //生徒の場合は自分自身を対象とする
      $items = $items->mydata($user->user_id);
    }
    $items = $this->_search_scope($request, $items);
    $count = $items->count();
    $items = $items->paginate($param['_line']);

    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'type_name' => [
        'label' => '種別',
      ],
      'body' => [
        'label' => '内容',
        'link' => 'show',
      ],
    ];
    $fields['target_user_name'] = [
      'label' => '対象者',
    ];
    /*
    $fields['publiced_at'] = [
      'label' => '公開日',
    ];
    */
    $fields['create_user_name'] = [
      'label' => '起票者',
    ];
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
    ];
    $fields['buttons'] = [
      'label' => '操作',
      'button' => ['edit', 'delete']
    ];
    return ['items' => $items, 'fields' => $fields, 'count' => $count];
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
    //ステータス 検索
    if(isset($request->search_status)){
      $items = $items->where('status',$request->search_status);
    }
    //種別 検索
    if(isset($request->search_type)){
      if($request->search_type==='private'){
        $items = $items->where('publiced_at', '>=' , date('Y-m-d'));
      }
      else {
        $items = $items->where('type',$request->search_type);
      }
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
    $form['target_user_id'] = $this->get_target_user_id($request);
    $form['importance'] = 0;
    return $form;
  }

  public function update_form(Request $request){
    $form = [];
    if(!empty($request->get('publiced_at'))){
      $form['publiced_at'] = $request->get('publiced_at');
    }
    $form['type'] = $request->get('type');
    $form['title'] = $request->get('title');
    $form['importance'] = $request->get('importance');
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
      'type_name' => [
        'label' => '種別',
      ],
      'body' => [
        'label' => '内容',
      ],
    ];
    if($this->is_manager_or_teacher($param['user']->role)===true){
      //生徒以外の場合は、対象者も表示する
      $fields['target_user_name'] = [
        'label' => '対象者',
      ];
      $fields['create_user_name'] = [
        'label' => '起票者',
      ];
      $fields['publiced_date'] = [
        'label' => '公開日',
      ];
    }
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
    ];
    $fields['updated_date'] = [
      'label' => __('labels.upd_datetime'),
    ];

    return view('components.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
      ->with($param);
  }

}
