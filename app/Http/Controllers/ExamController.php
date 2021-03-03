<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;

class ExamController extends SchoolGradeController
{
    //
    public $domain = "exams";
    public $domain_name = "試験";
    public function model(){
      return Exam::query();
    }
    public function _store(Request $request)
    {
      $res = $this->transaction($request, function() use ($request){
        $item = new Exam;
        $item = $item->add($request->all());
        
        return $this->api_response(200, '', '', $item);
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
     }
/*
    public function index(Request $request){
      $param = $this->get_param($request);
      $param['items'] = $this->model()->all();
      return view($this->domain.'.list')->with($param);
    }

    public function create(Request $request){
      $param = $this->get_param($request);

      return view($this->domain.'.create')->with($param);
    }

    public function store(Request $request){
      $param = $this->get_param($request);

      $item = new Exam;
      $item->add($request->all());

      return back()->withInput();
    }
*/
}
