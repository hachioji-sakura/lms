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
            "link" => function($row){
              return "/agreement_statements?agreement_id=".$row->id;
            },
          ],
          'statement_summary' =>[
            'label' => '明細概要',
          ],
          'buttons' => [
            'label' => '操作',
            'button' => [
              'confirm' => [
                'method' => 'ask/confirm',
                'label' => '承認依頼',
                'style' => 'primary',
              ],
            ],
          ],
        ];
        $param = [
          'items' => $this->model()->search($request)->paginate(),
          'search_word' => '',
          'fields' => $fields,
          'domain' => $this->domain,
          'domain_name' => "契約管理",
          'user' => Auth::user()->details(),
        ];
        return view($this->domain.'.list')->with($param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $param = [
          'student_parents' => StudentParent::all(),
          'domain' => $this->domain,
        ];
        return view($this->domain.'.create')->with($param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
       return $res = $this->transaction($request, function() use($request){
          $student = Student::find($request->get('agreements')['student_id']);
          if($student->enable_agreements->count() > 0){
            $parent_agreement = $student->enable_agreements->first();
            $parent_agreement->status = 'cancel';
            $parent_agreement->save();
            $parent_agreement_id = $parent_agreement->id;
          }else{
            $parent_agreement_id = null;
          }
          $item = new Agreement;
          $item->add($request,'commit',$parent_agreement_id);
          return $this->api_response(200, '', '', $item);
        },__('labels.registered'), __FILE__, __FUNCTION__, __LINE__);
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
        return view('agreement_statements.list');
    }

    public function ask_page(Request $request, $id, $method){
      $param = $this->get_param($request,$id);
      return view('agreements.agreement_confirm')->with($param);
    }

    public function admission_mail_send(Request $request, $id){
      $param = $this->get_param($request, $id);
      $access_key = $this->create_token(2678400);
      $res = $this->transaction($request, function() use ($request,$param, $access_key){
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

}
