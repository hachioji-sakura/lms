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
           'lesson_name' => [
             'label' => 'レッスン',
           ],
           'course_type_name' => [
             'label' => 'コース',
           ],
           'course_minutes_name' =>[
             'label' => '時間',
           ],
           'lesson_week_count' =>[
             'label' => '週コマ数',
           ],
           'teacher_name' => [
             'label' => '対象講師',
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
       ];
       return view($this->domain.'.list')->with($param);
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }




}
