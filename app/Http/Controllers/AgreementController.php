<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agreement;
use App\Models\AgreementStatement;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;


class AgreementController extends MilestoneController
{
    public $domain = 'agreements';
    public $domain_name = '契約';
    public function model(){
      return Agreement::query();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $fields = [
          'title' => [
            'label' => '概要',
            "link" => 'show',

          ],
          'status_name' =>[
            'label' =>  'ステータス',
          ],
          'student_parent_name' => [
            'label' => '契約者氏名'
          ],
          'format_entry_date' => [
            'label' => '登録日',
          ],
          'format_start_date' => [
            'label' => '開始日',
          ],
          'format_end_date' => [
            'label' => '終了日',
          ],
          /*
          'statement_summary' =>[
            'label' => '明細概要',
          ],
          */
          'buttons' => [
            'label' => '操作',
            'button' => [
              'confirm' => [
                'method' => 'ask/confirm',
                'icon' => 'envelope',
                'label' => __('labels.send_approval'),
                'style' => 'primary',
                'type' => function($row){
                    if($row->status == 'new' && $row->student->status == 'regular' && !$row->is_agreement_confirm_send()){
                      return true;
                    }else{
                      return false;
                    }
                  }
              ],
              'remind' => [
                'method' => 'ask/confirm',
                'icon' => 'envelope',
                'label' => __('labels.remind_button'),
                'style' => 'secondary',
                'type' => function($row){
                    if($row->status == 'new' && $row->student->status == 'regular' && $row->is_agreement_confirm_send()){
                      return true;
                    }else{
                      return false;
                    }
                  }
              ],
              'edit' => [
                'type' => function($row){
                  if($row->status == "dummy"){
                    return true;
                  }else{
                    return false;
                  }
                }
              ],
            ],
          ],
        ];
        $param = $this->get_param($request);
        $param['items'] = $this->model()->search($request)->paginate();
        $param['new_item_count'] = $this->model()->findStatuses(['new'])->count();
        $param['search_word'] = '';
        $param['fields'] = $fields;
        $param['domain'] = $this->domain;
        $param['domain_name'] = $this->domain_name;
        $param['user'] = Auth::user()->details();
        return view($this->domain.'.list')->with($param);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $param = $this->get_param($request,$id);
        $param['is_money_edit'] = false;
        return view($this->domain.'.details')->with($param);
    }

    public function edit(Request $request, $id){
      $param = $this->get_param($request,$id);
      $param['is_money_edit'] = true;
      $param['_edit'] = true;
      return view($this->domain.'.details')->with($param);
    }

    public function _update(Request $request, $id){

      $res = $this->transaction($request, function() use ($request,$id){
        $item = $this->model()->find($id);
        $item->change($request);
        return $this->api_response(200, '', '', $item);
      },'更新しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
    }


    public function ask_page(Request $request, $id, $method){
      $param = $this->get_param($request,$id);
      return view('agreements.agreement_confirm')->with($param);
    }

    public function admission_mail_send(Request $request, $id){
      $param = $this->get_param($request, $id);
      $access_key = $this->create_token(2678400);
      $res = $this->transaction($request, function() use ($request,$param, $access_key){
        $item = $param['item'];
        $item->entry_fee = $request->agreements['entry_fee'];
        $item->monthly_fee = $request->agreements['monthly_fee'];
        $item->save();
        //料金が変更されていたら更新
        foreach($request->get('agreement_statements') as $statement_id => $value){
          $statement = AgreementStatement::find($statement_id);
          if($statement->tuition != $value['tuition']){
            $statement->tuition = $value['tuition'];
            $statement->save();
          }
        }
        $agreement = $param['item'];
        $ask = $agreement->agreement_ask($param['user']->user_id, $access_key, 'agreement_confirm');
        return $this->api_response(200, '', '', $ask);
      }, '契約更新連絡', __FILE__, __FUNCTION__, __LINE__ );
      return $this->save_redirect($res, [], '契約更新の承認依頼を送信しました。');
    }

    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }elseif($user->details()->role != "manager"){
        abort(403);
      }
      $ret = $this->get_common_param($request);
      if(is_numeric($id) && $id > 0){
        $item = $this->model()->where('id','=',$id)->first();
        $ret['item'] = $item;
      }
      return $ret;
    }

}
