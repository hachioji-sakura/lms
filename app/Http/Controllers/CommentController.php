<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\Student;
use DB;
class CommentController extends MilestoneController
{
    public $domain = 'comments';
    public $table = 'comments';
    public $domain_name = 'コメント';
    public function model(){
      return Comment::query();
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
      if($this->i($user->role)!==true){
        //生徒の場合は自分自身を対象とする
        $items = $items->where('target_user_id',$user->user_id);
      }
      $items = $this->_search_scope($request, $items);
      $items = $this->_search_pagenation($request, $items);

      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $create_user = $item->create_user->details();
        $item->create_user_name = $create_user->name;
        unset($item->create_user);
        $target_user = $item->target_user->details();
        $item->target_user_name = $target_user->name;
        unset($item->target_user);
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
      $fields['target_user_name'] = [
        'label' => '対象者',
      ];
      $fields['create_user_name'] = [
        'label' => '起票者',
      ];

      $fields['publiced_at'] = [
        'label' => '公開日',
      ];
      $fields['created_at'] = [
        'label' => '登録日時',
      ];
      $fields['buttons'] = [
        'label' => '操作',
        'button' => ['edit', 'delete']
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
      $user = $this->login_details();
      $form = [];
      $form['publiced_at'] = '9999-12-31';
      if($this->is_manager_or_teacher($user->role)){
        $form['publiced_at'] = date('Y-m-d');
      }
      $form['create_user_id'] = $user->user_id;
      $form['type'] = $request->get('type');
      $form['title'] = $request->get('title');
      $form['body'] = $request->get('body');
      if($this->is_student($user->role)===true){
        //生徒の場合は自分自身を対象とする
        $form['target_user_id'] = $user->user_id;
      }
      else {
        if($request->has('student_id')){
          $u = Student::where('id',$request->get('student_id'));
        }
        else if($request->has('teacher_id')){
          $u = Teacher::where('id',$request->get('teacher_id'));
        }
        else if($request->has('manager_id')){
          $u = Manager::where('id',$request->get('manager_id'));
        }
        $form['target_user_id'] = $u->user_id;
      }
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
      if($this->is_manager_or_teacher($param['user']->role)===true){
        //生徒以外の場合は、対象者も表示する
        $fields['target_user_name'] = [
          'label' => '対象者',
        ];
        $fields['create_user_name'] = [
          'label' => '起票者',
        ];
        $fields['publiced_at'] = [
          'label' => '公開日',
        ];
      }
      $fields['created_at'] = [
        'label' => '登録日時',
      ];
      $fields['updated_at'] = [
        'label' => '更新日時',
      ];

      return view('components.page', [
        'action' => $request->get('action'),
        '_page_origin' => str_replace('_', '/', $request->get('_page_origin')),
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
      $fields['target_user_name'] = [
        'label' => '対象者',
      ];
      $fields['create_user_name'] = [
        'label' => '起票者',
      ];
      $fields['publiced_at'] = [
        'label' => '公開日',
      ];

      return view('comments.publiced', [
        'fields'=>$fields])
        ->with($param);
    }
    /**
     * コメント公開
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publiced(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      //生徒・親の場合は、対象生徒指定があり、かつ、関係者である場合操作可能
      if(!$this->is_manager($param['user']->role)){
        //事務以外アクセス不可
        abort(403);
      }
      $this->model()->where('id',$id)->update('publiced_at', date('Y-m-d'));
      $update_message = "コメントを公開しました。";
      return $this->save_redirect($res, $param, $update_message, str_replace('_', '/', $request->get('_page_origin')));
    }

}
