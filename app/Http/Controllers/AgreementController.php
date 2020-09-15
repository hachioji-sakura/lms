<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agreement;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;


class AgreementController extends MilestoneController
{
    public $domain = 'agreements';
    public function model(){
      return Agreement::query();
    }
    public $fields = [
              'id' => [
                'label' => 'ID',
              ],
              'title' => [
                'label' => '概要',
              ],
              'student_parent_name' => [
                'label' => '契約者',
              ],
              'entry_fee' => [
                'label' => '入会金',
              ],
              'membership_fee'=> [
                'label' => '月会費'
              ]
            ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $param = [
          'items' => $this->model()->paginate(),
          'search_word' => '',
          'fields' => $this->fields,
          'domain' => $this->domain,
          'domain_name' => $this->domain,
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
        $item = new Agreement;
        $item->fill($request->all());
        $item->status = 'new';
        $item->create_user_id = Auth::user()->id;
        $res = $this->transaction($request, function() use($item, $request){
          $request->validate([
            'title' => 'string',
            'entry_fee' => 'integer',
            'membership_fee' => 'integer',
            'entry_date' => 'datetime',
            'student_parent_id' => 'integer|required',
          ]);
          $item->save();
        },__('labels.registered'), __FILE__, __FUNCTION__, __LINE__);
        return $res;
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
    }

}
