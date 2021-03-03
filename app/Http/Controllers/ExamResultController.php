<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\ExamResult;
use DB;

class ExamResultController extends MilestoneController
{
    //
    public $domain = 'exam_results';

    public function model(){
      return ExamResult::query();
    }

    public function _store(Request $request)
    {
      $res = $this->transaction($request, function() use ($request){
        $item = new ExamResult;
        $item = $item->add($request->all());
        if($request->hasFile('upload_file')){
          if ($request->file('upload_file')->isValid([])) {
            $item->file_upload($request->file('upload_file'));
          }
        }
        return $this->api_response(200, '', '', $item);
      }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
      return $res;
     }

     public function create(Request $request){
       $param = $this->get_param($request);
       $subjects = Subject::all()->pluck('name','id');
       if($request->has('exam_id')){
         $exam_id = $request->get('exam_id');
       }
       return view($this->domain.'.create', [
         '_edit' => false,
         'exam_id' => $exam_id,
         'subjects' => $subjects,
       ])->with($param);
     }

     public function edit(Request $request, $id){
       $param = $this->get_param($request,$id);
       $subjects = Subject::all()->pluck('name','id');
       return view($this->domain.'.create', [
         '_edit' => true,
         'subjects' => $subjects,
       ])->with($param);
     }
}
