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
        'body' => [
          'label' => '説明',
        ],
        'description' => [
          'label' => '内容',
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
        ],
        'name' => [
          'label' => '資料名',
          'link' => 'show',
        ],
        'create_user_name' => [
          'label' => '登録者',
        ],
        'publiced_at' => [
          'label' => '公開日',
        ],
        'created_at' => [
          'label' => '登録日',
        ],
        'buttons' => [
          'label' => '操作',
          'button' => ['edit', 'delete']
        ],
      ];

      return ["items" => $items, "fields" => $fields];
    }
}
