<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\GeneralAttribute;
use App\Models\Subject;

class ExamController extends SchoolGradeController
{
    //
    public $domain = "exams";
    public $domain_name = "試験";
    public function model(){
      return Exam::query();
    }

    public function show(Request $request, $id)
    {
      $param = $this->get_param($request, $id);
      $fields = [
        'subject_name' => [
          'label' => __('labels.subjects'),
          'blank' => true,
          'link' => function($row){
            if(empty($row->s3_url)){
              return "";
            }
            return $row->s3_url;
          }
        ],
        'point_per_max' => [
          'label' => __('labels.point'),
        ],
        'deviation' => [
          'label' => __('labels.deviation'),
        ],
      ];

      return view($this->domain.'.page', [
        'action' => $request->get('action'),
        'fields'=>$fields])
        ->with($param);
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

     public function edit(Request $request, $id)
     {
       $param = $this->get_param($request, $id);
       $grades = GeneralAttribute::findKey('grade')->pluck('attribute_name','attribute_value');
       $subjects = Subject::all()->pluck('name','id');
       return view($this->domain.'.create', [
         '_edit' => true,
         'grades' => $grades,
         'subjects' => $subjects,
       ])->with($param);
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
