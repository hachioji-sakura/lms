<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

//class EventController extends Controller
class EventController extends MilestoneController
{
  public $domain = 'events';
  public $table = 'events';
  public function model(){
    return Event::query();
  }
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
  //  public function index()
  //  {
        //
        //$items = Event::all();
        //
        //$items = Event::where('id','<=',2)->orderBy('id','desc')->get();
        //return $items;
  //  }

/**
 * イベント画面表示
 *
 */
 public function show(Request $request, $id)
 {
  $param = $this->get_param($request, $id);
  $fields = [
    'title' => [
      'label' => '件名'
    ],
    'event_from_date' => [
      'label' => '開催期間_始'
    ],
    'event_to_date' => [
      'label' => '開催期間_終'
    ],
    'response_from_date' => [
      'label' => '回答期間_始'
    ],
    'response_to_date' => [
      'label' => '回答期間_終'
    ],
    'body' => [
      'label' => '備考'
    ],
    'create_user_id' => [
      'label' => '作成ユーザID'
    ],
　];

  return view('components.page', [
  'action' => $request->get('action'),
  'fields'=>$fields])
  ->with($param);

 }

 /**
  * 検索～一覧
  *
  * @param  \Illuminate\Http\Request  $request
  * @return [Collection, field]
  */
 public function search(Request $request)
 {
   $param = $this->get_param($request);
   $items = $this->model();
   $user = $this->login_details($request);
   $items = $this->_search_scope($request, $items);
   $items = $items->paginate($param['_line']);
   $fields = [
     'title' => [
       'label' => '件名'
     ],
     'event_from_date' => [
       'label' => '開催期間_始'
     ],
     'event_to_date' => [
       'label' => '開催期間_終'
     ],
     'response_from_date' => [
       'label' => '回答期間_始'
     ],
     'response_to_date' => [
       'label' => '回答期間_終'
     ],
     'body' => [
       'label' => '備考'
     ],
     'create_user_id' => [
       'label' => '作成ユーザID'
     ],
   ];
   return ['items' => $items, 'fields' => $fields];

 }


}
