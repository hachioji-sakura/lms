<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserCalendarMember;

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
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'remind' => false,
      'token' => $this->create_token(1728000),    //token期限＝20日
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'search_status'=>$request->status,
      'access_key' => $request->key,
      'attributes' => $this->attributes(),
    ];
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
      $ret['item'] = $item->calendar->details();
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
      $item->dispose();
      return $this->api_response(200, '', '', $item);
    }, 'カレンダーメンバー削除', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }

}
