<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserCalendarMember;
use App\Models\PlaceFloorSheat;

class UserCalendarMemberController extends UserCalendarController
{
  public $domain = 'calendar_members';
  public $table = 'user_calendar_members';

  public function model(){
    return UserCalendarMember::query();
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    //$user = User::where('id', 607)->first()->details();

    $ret = $this->get_common_param($request);
    if($request->has('cancel_reason')){
      $ret['cancel_reason'] = $request->get('cancel_reason');
    }
    if($request->has('access_key')){
      $ret['token'] = $request->get('access_key');
    }
    if(is_numeric($id) && $id > 0){
      $user_id = -1;
      if($request->has('user')) $user_id = $request->get('user');
      $item = $this->model()->where('id',$id)->first();
      if(!isset($item)){
        abort(404, 'ページがみつかりません(1)');
      }
      if(!isset($user) && !empty($user_id)){
        $user = User::where('id', $user_id)->first();
        if(!isset($user)){
          abort(403, '有効期限が切れています(4)');
        }
        $user = $user->details();
        $ret['user'] = $user;
      }
      if(isset($user)){
        if($this->is_manager($user->role)===false){
          if($item->calendar->is_access($user->user_id)===false){
            abort(403, 'このページにはアクセスできません(1)');
          }
        }
      }
      else {
        abort(403, 'このページにはアクセスできません(2)');
      }
      $ret['item'] = $item->details();
      $ret['member_id'] = $id;
    }
    return $ret;
  }
  /**
   * 詳細画面表示
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $param['fields'] = $this->show_fields();
    if($request->has('user')){
      return view('calendars.simplepage', ["subpage"=>'' ])->with($param);
    }
    return view('calendars.page', [
      'action' => $request->get('action')
    ])->with($param);
  }
  /**
   * 授業予定削除処理
   *
   * @return \Illuminate\Http\Response
   */
  public function _delete(Request $request, $id)
  {
    $res = $this->transaction($request, function() use ($request, $id){
      $param = $this->get_param($request, $id);
      $calendar = $param["item"];
      $item = $this->model()->where('id',$id)->first();
      if(!isset($item)) return $this->not_found();
      $this->send_slack('カレンダーメンバー削除/ id['.$id.']', 'info', 'カレンダーメンバー削除');
      $item->dispose($param['user']->user_id);
      return $this->api_response(200, '', '', $item);
    }, 'カレンダーメンバー削除', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }

  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request)
  {
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
    $user = $param['user'];
    if(!$this->is_manager($user->role)){
      //事務以外 一覧表示は不可能
      abort(403);
    }
    $_table = $this->search($request);
    return view($this->domain.'.lists', $_table)
      ->with($param);
  }
  public function search(Request $request)
  {
    $param = $this->get_param($request);
    if(!isset($param['user'])) return $this->forbidden();
    if($this->is_manager($param['user']->role)!=true) return $this->forbidden();
    $items = $this->model();
    $items = $this->_search_scope($request, $items);
    $items = $items->paginate($param['_line']);

    $fields = [
      "calendar_id" => [
        "label" => __('labels.calendars'),
        "link" => function($row){
          return "/calendars?id=".$row['calendar_id'];
        },
      ],
      "status_name" => [
        "label" => __('labels.status'),
      ],
      "user_name" => [
        "label" => __('labels.target_user'),
      ],
      "place_floor_sheat_id" => [
        "label" => __('labels.sheat'),
      ],
      "rest_result" => [
        "label" => __('labels.rest').__('labels.result'),
      ],
      "str_exchange_limit_date" => [
        "label" => __('labels.exchange_limit_date'),
      ],
      "buttons" => [
        "label" => __('labels.control'),
        "button" => [
          "edit",
          "delete"]
      ]
    ];
    return ["items" => $items, "fields" => $fields];
  }

  /**
   * フィルタリングロジック
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  Collection $items
   * @return Collection
   */
  public function _search_scope(Request $request, $items)
  {
    $form = $request->all();

    //ID 検索
    if(isset($form['id'])){
      $items = $items->where('id',$form['id']);
    }
    if(isset($form['calendar_id'])){
      $items = $items->where('calendar_id',$form['calendar_id']);
    }
    //ステータス 検索
    if(isset($form['search_status'])){
      if(gettype($form['search_status']) == "array") $items = $items->findStatuses($form['search_status']);
      else $items = $items->findStatuses(explode(',', $form['search_status'].','));
    }
    //授業タイプ 検索
    if(isset($form['rest_type'])){
      $_param = "";
      if(gettype($form['rest_type']) == "array") $_param  = $form['rest_type'];
      else $_param = explode(',', $form['rest_type'].',');
      $items = $items->findRestType($_param);
    }
    //日付検索
    $from_date = "";
    $to_date = "";
    if(isset($request->from_date)){
      $from_date = $request->from_date;
      if(mb_strlen($from_date) < 11) $from_date .=' 00:00:00';
    }
    if(isset($request->to_date)){
      $to_date = $request->to_date;
      if(mb_strlen($to_date) < 11) $to_date .=' 23:59:59';
    }
    if(!empty($from_date) || !empty($to_date)){
      $items = $items->findExchangeLimitDate($from_date, $to_date);
    }
    //講師ID
    if(isset($form['teacher_id'])){
      $teacher = Teacher::where('id',$form['teacher_id'])->first();
      if(isset($teacher)) $items = $items->where('user_id', $teacher->user_id);
    }
    //生徒ID
    if(isset($form['student_id'])){
      $student = Student::where('id',$form['student_id'])->first();
      $items = $items->where('user_id', $student->user_id);
    }

    //検索ワード
    if(isset($request->search_keyword)){
      $items = $items->searchWord($request->search_keyword);
    }
    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }

    return $items;
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $sheats = PlaceFloorSheat::where('place_floor_id', $param['item']->calendar->place_floor_id)->orderBy('sort_no')->get();
    $param['sheats'] = $sheats;
    return view($this->domain.'.create', [
      '_edit' => true])
      ->with($param);
  }

}
