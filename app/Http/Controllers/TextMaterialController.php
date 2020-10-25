<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextMaterial;

class TextMaterialController extends MilestoneController
{
    public $domain = 'text_materials';
    public $table = 'text_materials';
    public function model(){
      return TextMaterial::query();
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
        'name' => [
          'label' => '保存ファイル名',
        ],
        'description' => [
          'label' => '説明',
        ],
        'type' => [
          'label' => 'mimetype',
        ],
        'size' => [
          'label' => 'ファイルサイズ',
        ],
        'type' => [
          'label' => 'mimetype',
        ],
        's3_url' => [
          'label' => 'S3ダウンロードURL',
        ],
        'create_user_name' => [
          'label' => '登録者',
        ],
        'created_date' => [
          'label' => '登録日',
        ],
        'updated_date' => [
          'label' => '更新日',
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
        'id' => [
          'label' => 'ID',
          'link' => 'show',
        ],
        'name' => [
          'label' => '資料名',
          'link' => function($row){
            return $row->s3_url;
          },
          'target' => '__blank',
        ],
        'publiced_date' => [
          'label' => '公開日',
        ],
        'create_user_name' => [
          'label' => '登録者',
        ],
        'created_date' => [
          'label' => '登録日',
        ],
        'buttons' => [
          'label' => '操作',
          'button' => ['edit', 'delete']
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
      $form['publiced_at'] = $request->get('publiced_at');
      $form['description'] = $request->get('description');
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
      if(empty($form['publiced_at'])){
        return $this->bad_request('リクエストエラー', '公開日='.$form['publiced_at']);
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
     if(!$request->hasFile('upload_file')){
       return $this->bad_request('ファイルがありません');
     }
     $res = $this->transaction($request, function() use ($request){
       $form = $this->create_form($request);
       $request_file = $request->file('upload_file');
       $user = $this->login_details($request);
       $text_material = new TextMaterial;
       $s3 = $this->s3_upload($request_file, config('aws_s3.text_material_folder'));
       $form['name'] = $request_file->getClientOriginalName();
       $form['s3_url'] = $s3['url'];
       $form['type'] = $request_file->guessClientExtension();
       $form['size'] = $request_file->getClientSize();
       $form['create_user_id'] = $user->user_id;
       $text_material->fill($form)->save();
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
        $user = $this->login_details($request);
        $text_material = TextMaterial::find($id)->first();
        if($request->hasFile('upload_file')){
          $this->s3_delete($text_material->s3_url);
          $request_file = $request->file('upload_file');
          $s3 = $this->s3_upload($request_file, config('aws_s3.text_material_folder'));
          $form['name'] = $request_file->getClientOriginalName();
          $form['s3_url'] = $s3['url'];
          $form['type'] = $request_file->guessClientExtension();
          $form['size'] = $request_file->getClientSize();
        }
        $form['create_user_id'] = $user->user_id;
        $text_material->fill($form)->save();
        return $this->api_response(200, '', '', $text_material);
      }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }
}
