<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextMaterial;
use App\User;
use App\Models\Teacher;
use App\Models\Curriculum;
use App\Models\Subject;

class TextMaterialController extends MilestoneController
{
    public $domain = 'text_materials';
    public $table = 'text_materials';
    public function model(){
      return TextMaterial::query();
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
      if(!isset($user)) {
        abort(403);
      }
      if($this->is_student_or_parent($user->role)) {
        abort(403);
      }
      $ret = $this->get_common_param($request);
      $target_user_id = 0;
      if($request->has('target_user_id')){
        $target_user_id = $request->get('target_user_id');
      }

      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id','=',$id)->first();
        if(isset($item['create_user_id']) && $this->is_student($user->role) &&
          $item['create_user_id'] !== $user->user_id){
            //生徒は自分の起票したものしか編集できない
            abort(404);
        }
        $ret['item'] = $item;
        $target_user_id = $item->target_user_id;
      }
      $ret['target_user_id'] = $target_user_id;

      if($target_user_id > 0){
        $target_user = User::find($target_user_id);
        $lessons = collect($target_user->get_tags('lesson'));
        $ret['lessons'] = $lessons;
        $ret['has_english_lesson'] = $lessons->pluck('tag_value')->contains(2);
      }
      else {
        if($this->is_manager($ret['user']->role)){
          $ret['teachers'] = Teacher::where('status', 'regular')->get();
        }
      }
      $ret['curriculums'] = Curriculum::all();
      $ret['subjects'] = Subject::all();
      $ret['shared_users'] = Teacher::where('status','regular')->get()
                            ->map(function($item){
                                      return $item->user;
                                  });//今は有効な講師のみ
        $ret['bulk_action'] = $this->get_bulk_action();
      return $ret;
    }

    public function get_bulk_action(){
      return [
        'check_box' => true,
        'label' => __('labels.bulk').__('labels.share'),
        'icon' => "share",
        'url' => '/text_materials/bulk_shared',
      ];
    }

    public function bulk_shared_page(Request $request){
      $param = $this->get_param($request);
      $param['action_url'] = "/".$this->domain."/bulk_shared";
      if($request->has('list_check')){
        $param['items'] = $this->model()->find($request->list_check);
      }else{
        $param['items'] = collect([]);
      }

      return view($this->domain.'.bulk_shared_page')->with($param);
    }

    public function bulk_shared(Request $request){
      $param = $this->get_param($request);
      $items = $this->model()->find($request->text_material_ids);

      $res =  $this->transaction($request, function() use ($request, $items){
        if($request->has('shared_user_ids')){
          $items->map(function($item) use($request){
            if($request->method == "sync"){
              return $item->shared_users()->sync($request->shared_user_ids);
            }elseif($request->method == "attach"){
              return $item->shared_users()->attach($request->shared_user_ids);
            }
          });  
        }
        return $this->api_response(200, '', '', $items);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, '更新しました。');
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
        'description' => [
          'label' => '説明',
        ],
        's3_url' => [
          'label' => 'ダウンロード',
          'size' => 6,
        ],
        'type' => [
          'label' => 'mimetype',
          'size' => 6,
        ],
        'target_user_name' => [
          'label' => '担当者',
          'size' => 6,
        ],
        'create_user_name' => [
          'label' => '登録者',
          'size' => 6,
        ],
        'created_date' => [
          'label' => '登録日',
          'size' => 6,
        ],
      ];

      return view('components.page', [
        'action' => $request->get('action'),
        'fields'=>$fields])
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
      $param = $this->get_param($request);
      $items = $this->model();
      $items = $this->_search_scope($request, $items);
      $items = $items->paginate($param['_line']);

      $fields = [
        'name' => [
          'label' => '資料名',
          'check_box' => true,
          'link' => function($row){
            return $row->s3_url;
          },
          'target' => '__blank',
        ],
        'is_publiced_label' => [
          'label' => '公開',
        ],
        'target_user_name' => [
          'label' => '担当者',
        ],
        'created_date' => [
          'label' => '登録日',
        ],
        'buttons' => [
          'label' => '操作',
          'button' => [
            "to_calendar" => [
              "method" => "shared",
              "label" => "共有設定",
              "style" => "warning",
            ],
            "detail" => [
              "method" => "",
              "label" => "詳細",
              "style" => "secondary",
            ],
            'edit', 'delete'
          ]
        ],
      ];

      return ["items" => $items, "fields" => $fields];
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
      $user = $this->login_details($request);
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
            $items->orWhere('name','like',$_like)->orWhere('description','like',$_like);
          }
        });
      }
      //登録日付でソート
      if(isset($request->is_asc) && $request->get('is_asc')==1){
        $items = $items->orderBY('created_at', 'asc');
      }
      else {
        $items = $items->orderBY('created_at', 'desc');
      }

      return $items;
    }
    /**
     * 新規登録用フォーム
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function create_form(Request $request){
      $user = $this->login_details($request);
      $form = [];
      $form['create_user_id'] = $user->user_id;
      $form['target_user_id'] = $request->get('target_user_id');
      $form['publiced_at'] = "9999-12-31";
      $form['description'] = $request->get('description');
      $form['name'] = $request->get('name');
      $form['create_user_id'] = $user->user_id;
      if($request->hasFile('upload_file')){
        $request_file = $request->file('upload_file');
        $form['s3_alias'] = $request_file->getClientOriginalName();
        $form['type'] = $request_file->guessClientExtension();
        $form['size'] = $request_file->getClientSize();
      }
      if($request->has('is_public')){
        if(intval($request->get('is_public'))==1){
          $form['publiced_at'] = date('Y-m-d');
        }
      }

      return $form;
    }
    /**
     * 更新用フォーム
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function update_form(Request $request){
      return $this->create_form($request);
    }
    /**
     * データ更新時のパラメータチェック
     *
     * @return \Illuminate\Http\Response
     */
    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if(empty($form['name'])){
        return $this->bad_request('リクエストエラー', '名称='.$form['name']);
      }
      return $this->api_response(200, '', '');
    }
    /* 新規登録ロジック
    *
    * @return \Illuminate\Http\Response
    */
   public function _store(Request $request)
   {
     $res = $this->save_validate($request);
     if(!$this->is_success_response($res)){
       return $res;
     }
     //新規の場合は、ファイルは必須
     if(!$request->hasFile('upload_file')){
       return $this->bad_request('ファイルがありません');
     }
     $res = $this->transaction($request, function() use ($request){
       $form = $this->create_form($request);
       $text_material = new TextMaterial;
       $s3 = $this->s3_upload($request->file('upload_file'), config('aws_s3.text_material_folder'));
       $form['s3_url'] = $s3['url'];
       $text_material->fill($form)->save();
       if($request->has('curriculum_ids')){
         $text_material->curriculums()->sync($request->get('curriculum_ids'));
       }
       return $this->api_response(200, '', '', $text_material);
     }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
    }
    public function _update(Request $request, $id)
    {
      $res = $this->save_validate($request);
      if(!$this->is_success_response($res)){
        return $res;
      }
      $res =  $this->transaction($request, function() use ($request, $id){
        $form = $this->create_form($request);
        $text_material = TextMaterial::find($id);
        if($request->hasFile('upload_file')){
          $this->s3_delete($text_material->s3_url);
          $s3 = $this->s3_upload($request->file('upload_file'), config('aws_s3.text_material_folder'));
          $form['s3_url'] = $s3['url'];
        }
        $text_material->fill($form)->save();
        if($request->has('curriculum_ids')){
          $text_material->curriculums()->sync($request->get('curriculum_ids'));
        }
        return $this->api_response(200, '', '', $text_material);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
    /**
     * 詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function shared_page(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $teachers = Teacher::where('status', 'regular')->where('user_id', '!=', $param['item']->target_user_id);
      if($this->is_manager($param['user']->role)){
      }
      else if($this->is_teacher($param['user']->role)){
        $tags = $param['user']->get_tags('lesson');
        if($tags == null) abort(403);
        $teachers = $teachers->searchTags($tags);
      }
      else {
        abort(403);
      }
      $param['teachers'] = $teachers->get();
      return view($this->domain.'.shared', [])
        ->with($param);
    }
    public function shared(Request $request, $id)
    {
      $param = $this->get_param($request, $id);

      $res =  $this->transaction($request, function() use ($request, $id){
        $text_material = TextMaterial::find($id);
        if($request->has('shared_user_ids')){
          $text_material->shared_users()->sync($request->get('shared_user_ids'));
        }
        return $this->api_response(200, '', '', $text_material);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, $param, '更新しました。');
    }
}
