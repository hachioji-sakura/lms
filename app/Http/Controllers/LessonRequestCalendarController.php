<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonRequest;
use App\Models\LessonRequestDate;
use App\Models\LessonRequestCalendar;

class LessonRequestCalendarController extends UserCalendarController
{
  public $domain = 'lesson_request_calendars';
  public $table = 'lesson_request_calendars';
  public function model(){
    return LessonRequestCalendar::query();
  }
  public function show_fields($item=''){
    $base_ret = [
      'datetime' => [
        'label' => __('labels.datetime'),
      ],
      'status_name' => [
        'label' => __('labels.status'),
        'size' => 6,
      ],
      'place_floor_name' => [
        'label' => __('labels.place'),
        'size' => 6,
      ],
    ];
    $ret = [
      'user_name' => [
        'label' => __('labels.teachers'),
        'size' => 6,
      ],
      'teaching_type_name' => [
        'label' => __('labels.lesson_name'),
        'size' => 6,
      ],
      'subject' => [
        'label' => __('labels.subject'),
        'size' => 6,
      ],
      'student_name' => [
        'label' => __('labels.students'),
        'size' => 12,
      ],
    ];
    $ret['remark'] = [
      'label' => __('labels.remark'),
      'size' => 12,
    ];
    $ret = array_merge($base_ret, $ret);
    return $ret;
  }
  public function api_index(Request $request, $user_id=0, $from_date=null, $to_date=null)
  {

    set_time_limit(600);
    $param = $this->get_param($request);
    if(!empty($from_date) && strlen($from_date)===8){
      $from_date = date('Y-m-d', strtotime($from_date));
      $request->merge([
        'from_date' => $from_date,
      ]);
    }
    if(!empty($to_date) && strlen($to_date)===8){
      $to_date = date('Y-m-d', strtotime($to_date));
      $request->merge([
        'to_date' => $to_date,
      ]);
    }
    $user = $this->login_details($request);
    if(!isset($user)){
      return $this->forbidden();
    }
    $items = $this->model();
    $items = $this->_search_scope($request, $items);
    $items = $this->_search_pagenation($request, $items);
    $items = $this->_search_sort($request, $items);
    //\Log::warning("--------------UserCalendarController::api_index  start---------------------------");
    //\Log::warning($items->toSql());
    $items = $items->get();
    return $this->api_response(200, "", "", $items->toArray());
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
    //設定ID　検索
    if(isset($form['event_id'])){
      $items->whereIn('lesson_request_date_id',
        LessonRequestDate::whereIn('lesson_request_id',
          LessonRequest::where('event_id', $request->get('event_id'))->pluck('id')
        )->pluck('id'));
    }
    if(isset($form['is_fix_status'])){
      $items->whereIn('status', ['fix', 'complete']);
    }
    //ステータス 検索
    if(isset($form['search_status'])){
      if(gettype($form['search_status']) == "array") $items = $items->findStatuses($form['search_status']);
      else $items = $items->findStatuses(explode(',', $form['search_status'].','));
    }

    //場所 検索
    if(isset($form['search_place'])){
      $_param = "";
      if(gettype($form['search_place']) == "array") $_param  = $form['search_place'];
      else $_param = explode(',', $form['search_place'].',');
      $items = $items->findPlaces($_param);
    }

    if(isset($form['teaching_type'])){
      $_param = "";
      if(gettype($form['teaching_type']) == "array") $_param  = $form['teaching_type'];
      else $_param = explode(',', $form['teaching_type'].',');
      $items = $items->findTeachingType($_param);
    }

    //講師ID
    if(isset($form['teacher_id'])){
      $teacher = Teacher::where('id',$form['teacher_id'])->first();
      if(isset($teacher)) $items = $items->where('user_id', $teacher->user_id);
    }
    //生徒ID
    if(isset($form['student_id'])){
      $student = Student::where('id',$form['student_id'])->first();
      if(isset($student)) {
        $items->whereIn('lesson_request_date_id',
          LessonRequestDate::whereIn('lesson_request_id',
            LessonRequest::where('user_id', $student->user_id)->pluck('id')
          )->pluck('id')
        );
      }
    }
    //更新取得
    if(isset($form['update'])){
      $items = $items->where('updated_at','>',$form['update']);
    }

    //日付検索
    $from_date = "";
    $to_date = "";
    if(isset($request->search_from_date)){
      $from_date = $request->search_from_date;
      if(mb_strlen($from_date) < 11) $from_date .=' 00:00:00';
    }
    if(isset($request->search_to_date)){
      $to_date = $request->search_to_date;
      if(mb_strlen($to_date) < 11) $to_date .=' 23:59:59';
    }

    if(isset($request->from_date)){
      $from_date = $request->from_date;
      if(mb_strlen($from_date) < 11) $from_date .=' 00:00:00';
    }
    if(isset($request->to_date)){
      $to_date = $request->to_date;
      if(mb_strlen($to_date) < 11) $to_date .=' 23:59:59';
    }
    if(!empty($from_date) || !empty($to_date)){
      $items = $items->rangeDate($from_date, $to_date);
    }

    return $items;
  }
  public function complete_calendars(Request $request){
    return "fuga";
  }
}
