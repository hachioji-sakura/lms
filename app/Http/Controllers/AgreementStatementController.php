<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agreement;
use App\Models\AgreementStatement;
use Illuminate\Support\Facades\Auth;

class AgreementStatementController extends AgreementController
{
    public $domain = 'agreement_statements';
    public function model(){
      return AgreementStatement::query();
    }
    public $fields = [
              'id' => [
                'label' => 'ID',
              ],
              'studnet_id' => [
                'label' => '対象生徒',
              ],
              'teacher_id' => [
                'label' => '対象講師',
              ],
              'tuition' => [
                'label' => '料金',
              ],
            ];


     public function index(Request $request){
       $param = $this->get_param($request);
       $fields = [
         'teacher_name' => [
           'label' => '対象講師',
           'link' => 'show',
         ],
         'lesson_name' => [
           'label' => 'レッスン',
         ],
         'setting_summary' => [
           'label' => '設定概要',
         ],
         'tuition' => [
           'label' => '料金',
         ],
       ];
       $param = [
         'items' => $this->model()->search($request)->paginate(),
         'search_word' => '',
         'fields' => $fields,
         'domain' => $this->domain,
         'domain_name' => "契約明細管理",
         'user' => Auth::user()->details(),
         'agreements' => Agreement::enable()->get(),
       ];
       return view($this->domain.'.list')->with($param);
     }

     public function show(Request $request, $id){
       $param = $this->get_param($request,$id);
       $fields = [
         'teacher_name' =>[
           'label' => '講師名',
           'size' => '6',
         ],
         'lesson_name' =>[
           'label' => 'レッスン',
           'size' => '6',
         ],
         'course_type_name' =>[
           'label' => 'コース',
           'size' => '6',
         ],
         'course_minutes_name' =>[
           'label' => '時間',
           'size' => '6',
         ],
         'grade_name' =>[
           'label' => '学年',
           'size' => '6',
         ],
         'is_exam_name' =>[
           'label' => '受験',
           'size' => 6,
         ]
       ];
       return view('components.page',[
         'action' => 'show',
         'fields' => $fields,
         ])->with($param);
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
