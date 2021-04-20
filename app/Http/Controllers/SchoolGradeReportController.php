<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolGradeReport;

class SchoolGradeReportController extends MilestoneController
{
    //
    public $domain = 'school_grade_reports';
    public $domain_name = '成績詳細';

    public function model(){
      return SchoolGradeReport::query();
    }

    public function show(Request $request, $id)
    {
      $param = $this->get_param($request, $id);

      $fields = [
        'student_name' => [
          'label' => '生徒氏名'
        ],
        'semester_name' => [
          'label' => __('labels.semester'),
        ],
        'subject_name' => [
          'label' => __('labels.subjects')
        ],
        'report_point' => [
          'label' => __('labels.school_grades'),
        ],
      ];
      return view($this->domain.'.page', [
        'action' => $request->get('action'),
        'fields'=>$fields])
        ->with($param);
    }

}
