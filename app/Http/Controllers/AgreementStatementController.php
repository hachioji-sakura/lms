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


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
     {
         //
         $param = [
           'agreements' => Agreement::all(),
           'domain' => $this->domain,
         ];
         return view($this->domain.'.create')->with($param);
     }

     public function index(Request $request){
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




}
