<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ask;
use App\Models\AskComment;

class AskCommentController extends MilestoneController
{
  public $domain = 'ask_comments';
  public $table = 'ask_comments';
  public function model(){
    return AskComment::query();
  }
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['ask_id'] = $request->get('ask_id');
    $form['create_user_id'] = $user->user_id;
    $form['body'] = $request->get('body');
    return $form;
  }
  public function update_form(Request $request){
    $form = [];
    $form['body'] = $request->get('body');
    return $form;
  }
  public function save_validate(Request $request)
  {
    $form = $request->all();
    //保存時にパラメータをチェック
    if(empty($form['body'])){
      return $this->bad_request('リクエストエラー', '種別='.$form['type'].'/タイトル='.$form['title'].'/内容='.$form['body']);
    }
    return $this->api_response(200, '', '');
  }

  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);

    $fields = [
      'body' => [
        'label' => '内容',
      ],
    ];
    $fields['create_user_name'] = [
      'label' => '起票者',
    ];
    $fields['created_date'] = [
      'label' => __('labels.add_datetime'),
    ];
    return view('components.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
      ->with($param);
  }
  public function comment_create(Request $request, $ask_id)
  {
    $param = $this->get_param($request);
    $param['ask_id'] = $ask_id;
    return view($this->domain.'.create',['_edit' => false])
      ->with($param);
  }
  public function comment_edit(Request $request, $ask_id, $id)
  {
    $param = $this->get_param($request, $id);
    $param['ask_id'] = $ask_id;
    return view($this->domain.'.create',['_edit' => true])
      ->with($param);
  }
}
