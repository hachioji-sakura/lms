<?php

namespace App\Http\Controllers;
use App\Models\MailLog;
use Illuminate\Http\Request;

class MailLogController extends MilestoneController
{
  public $domain = "maillogs";
  public $table = "mails";
  public function model(){
    return MailLog::query();
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    if(!isset($user)) {
      abort(403);
    }
    if($this->is_manager_or_teacher($user->role)!==true){
      abort(403);
    }
    $ret = $this->get_common_param($request);
    if(is_numeric($id) && $id > 0){
      $ret['item'] = $this->model()->where('id','=',$id)->first();
    }

    return $ret;
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
    $items = $items->orderBy('id', 'desc')->paginate($param['_line']);

    $request->merge([
      '_sort_order' => 'desc',
      '_sort' => 'created_at',
    ]);
    if($request->has('is_asc') && $request->get('is_asc')==1){
      $request->merge([
        '_sort_order' => 'asc',
      ]);
    }

    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      "subject" => [
        "label" => "タイトル",
        "link" => "show",
      ],
      "to_address" => [
        "label" => "宛先",
      ],
      "status_name" => [
        "label" => "ステータス",
      ],
      "created_date" => [
        "label" => __('labels.add_datetime'),
      ],
    ];

    return ['items' => $items, 'fields' => $fields];
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
      $items = $items->fieldWhereIn($request->search_status);
    }
    //種別 検索
    if(isset($request->search_type)){
      $items = $items->findTemplates($request->search_type);
    }
    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }

    return $items;
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
    if(empty($form['subject']) || empty($form['body']) || empty($form['type'])){
      return $this->bad_request('リクエストエラー', '種別='.$form['type'].'/タイトル='.$form['title'].'/内容='.$form['body']);
    }
    return $this->api_response(200, '', '');
  }

  public function update_form(Request $request){
    $form = [];
    $form['status'] = $request->get('status');
    $form['subject'] = $request->get('subject');
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
      'to_address' => [
        'label' => '宛先',
        'size' => 8
      ],
      'status_name' => [
        'label' => 'ステータス',
        'size' => 4
      ],
      'subject' => [
        'label' => '件名',
        'size' => 12
      ],
      'body' => [
        'label' => '内容',
      ],
      'send_schedule' => [
        'label' => '送信予定',
        'size' => 6
      ],
      'locale_name' => [
        'label' => '言語',
        'size' => 6
      ],
      'template' => [
        'label' => 'テンプレート',
        'size' => 6
      ],
      'type' => [
        'label' => 'タイプ',
        'size' => 6
      ],
    ];
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
      'size' => 6
    ];
    $fields['updated_date'] = [
      'label' => __('labels.upd_datetime'),
      'size' => 6
    ];

    return view('components.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
      ->with($param);
  }
  /**
   * info@hachioji-sakura.com
   *
   * @return \Illuminate\Http\Response
   */
  public function info_mail_reply(Request $request)
  {
    $form_names = ['from_address'];
    foreach($form_names as $form_name){
      if(!$request->has($form_name) || empty($request->get($form_name))){
        return $this->bad_request();
      }
    }
    $form = $request->all();
    $res = $this->send_mail($form['from_address'], __('messages.info_mail_reply'), $form, 'text', 'info_mail_reply');
    if($this->is_success_response($res)){
      return $res;
    }
    return $this->error_response('メール送信失敗');
  }

}
