<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Ask;
use DB;
use View;
class AskController extends MilestoneController
{
  public $domain = 'asks';
  public $table = 'asks';
  public $domain_name = '依頼';
  public $status_update_message = [
          'new' => '新規依頼を登録しました',
          'commit' => '依頼を承認しました',
          'cancel' => '依頼をキャンセルしました',
        ];
  public $list_fields = [
    'end_dateweek' => [
      'label' => '締切',
    ],
    'type_name' => [
      'label' => '依頼',
      'link' => 'show',
    ],
    'status_name' => [
      'label' => 'ステータス',
    ],
    'target_user_name' => [
      'label' => '依頼者',
    ],
    'charge_user_name' => [
      'label' => '担当',
    ],
  ];
  public function model(){
    return Ask::query();
  }
  public function show_fields(){
    $ret = [
      'type_name' => [
        'label' => '依頼',
        'size' => 6,
      ],
      'status_name' => [
        'label' => 'ステータス',
        'size' => 6,
      ],
      'end_dateweek' => [
        'label' => '期限',
      ],
      'charge_user_name' => [
        'label' => '担当者',
        'size' => 6,
      ],
      'target_user_name' => [
        'label' => '対象者',
        'size' => 6,
      ],
      'body' => [
        'label' => '備考',
      ],
    ];
    return $ret;
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    if(!isset($user)) {
      abort(403);
    }
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_status'=>$request->status,
    ];

    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      $ret['item'] = $item->details();
    }
    return $ret;
  }

  public function index(Request $request)
  {
    if(!$request->has('_origin')){
      $request->merge([
        '_origin' => $this->domain,
      ]);
    }
    if(!$request->has('_line')){
      $request->merge([
        '_line' => $this->pagenation_line,
      ]);
    }
    if(!$request->has('_page')){
      $request->merge([
        '_page' => 1,
      ]);
    }
    else if($request->get('_page')==0){
      $request->merge([
        '_page' => 1,
      ]);
    }
    $param = $this->get_param($request);
    $_table = $this->search($request);
    $page_data = $this->get_pagedata($_table["count"] , $param['_line'], $param["_page"]);
    foreach($page_data as $key => $val){
      $param[$key] = $val;
    }
    return view($this->domain.'.lists', $_table)
      ->with($param);
  }
}
