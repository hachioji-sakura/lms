<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agreement;
use App\Models\AgreementStatement;

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
