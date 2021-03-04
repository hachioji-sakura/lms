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

     public function _update(Request $request, $id){
       $res =  $this->transaction($request, function() use ($request, $id){
         $item = $this->model()->where('id', $id)->first();
         $item->fill($request->all());
         $item->save();
         if($request->has('upload_file_delete') && $request->get('upload_file_delete')==1){
           $item->s3_delete($item->s3_url);
         }
         $file = null;
         if($request->hasFile('upload_file')){
           if ($request->file('upload_file')->isValid([])) {
             $item->file_upload($request->file('upload_file'));
           }
         }
         return $this->api_response(200, '', '', $item);
       }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
       return $res;
     }

     public function show_fields($type=''){
       $ret = [
         'subject_name' => [
           'label' => __('labels.subjects'),
         ],
         'point_per_max' => [
           'label' => __('labels.point'),
           'size' => 4,
         ],
         'deviation' => [
           'label' => __('labels.deviation'),
           'size' => 4,
         ],
         'average_point' => [
           'label' => __('labels.average_point'),
           'size' => 4,
         ],
         's3_alias' => [
           'label' => __('labels.file'),
         ],
       ];
       return $ret;
     }
}
