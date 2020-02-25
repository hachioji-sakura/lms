<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tuition;
use App\Models\UserCalendarMemberSetting;

class TuitionController extends MilestoneController
{
  public $domain = 'tuitions';
  public $table = 'tuitions';
  public function model(){
    return Tuition::query();
  }
  public function index(Request $request)
  {
    if(!$request->has('_origin')){
      $request->merge([
        '_origin' => $this->domain,
      ]);
    }
    $param = $this->get_param($request);
    $_table = $this->search($request);
    return view($this->domain.'.lists', $_table)
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
    $items = $this->model();
    $user = $this->login_details($request);
    $items = $this->_search_scope($request, $items);
    //$items = $this->_search_pagenation($request, $items);
    if(!(isset($user) && $this->is_manager($user->role))){
      $items = $items->where('publiced_at' , '<=', date('Y-m-d'));
    }
    if($request->has('teacher_id')){
      $items = $items->where('teacher_id', $request->get('teacher_id'));
    }
    else if($request->has('student_id')){
      $items = $items->where('student_id', $request->get('student_id'));
    }
    $items = $this->_search_sort($request, $items);
    $count = $items->count();
    $items = $items->get();
    foreach($items as $key => $item){
      $items[$key] = $item->details();
    }
    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'student_name' => [
        'label' => '生徒',
      ],
      'title' => [
        'label' => 'タイトル',
        'link' => 'show',
      ],
      'lesson_name' => [
        'label' => 'レッスン',
      ],
      'course_type_name' => [
        'label' => '授業形態',
      ],
      'grade_name' => [
        'label' => '学年',
      ],
      'teacher_name' => [
        'label' => '講師',
      ],
    ];
    $fields['buttons'] = [
      'label' => '操作',
      'button' => ['edit', 'delete']
    ];
    return ['items' => $items, 'fields' => $fields, 'count' => $count];
  }
  public function get_param(Request $request, $id=null){
    $user = $this->login_details($request);
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'origin' => $request->origin,
      'action' => $request->action,
      'item_id' => $request->item_id,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'search_type'=>$request->search_type,
      '_origin' => $request->get('_origin'),
      'attributes' => $this->attributes(),
    ];
    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      $ret['item'] = $item->details();
      $ret['student_id'] = $item->student_id;
      $student = $ret['item']->student;
      $calendar_settings = $student->user->calendar_setting();
      $ret['student'] = $student;
      $ret['calendar_settings'] = $calendar_settings;
    }
    else if($request->has('student_id')){
      $student = Student::where('id', $request->get('student_id'))->first();
      $calendar_settings = $student->user->calendar_setting();
      $ret['student'] = $student;
      $ret['calendar_settings'] = $calendar_settings;
    }

    return $ret;
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
    //ID 検索
    if(isset($request->id)){
      $items = $items->where('id',$request->id);
    }
    //type 検索
    if(isset($request->search_type) && !empty($request->search_type)){
      $_param = "";
      if(gettype($request->search_type) == "array") $_param  = $request->$search_type;
      else $_param = explode(',', $request->search_type.',');
      $items = $sitems->findTypes($_param);
    }

    //検索ワード
    if(isset($request->search_word)){
      $search_words = explode(' ', $request->search_word);
      $items = $items->where(function($items)use($search_words){
        foreach($search_words as $_search_word){
          if(empty($_search_word)) continue;
          $_like = '%'.$_search_word.'%';
          $items->orWhere('title','like',$_like)->orWhere('remark','like',$_like);
        }
      });
    }

    return $items;
  }
  public function create_form(Request $request){
    $user = $this->login_details($request);
    $form = [];
    $form['create_user_id'] = $user->user_id;
    $form['student_id'] = $request->get('student_id');
    $form['teacher_id'] = $request->get('teacher_id');
    $form['title'] = $request->get('title');
    $form['remark'] = $request->get('remark');
    if(empty($form['remark'])) $form['remark'] = '';
    $form['tuition'] = $request->get('tuition');
    $form['lesson'] = $request->get('lesson');
    $form['course_type'] = $request->get('course_type');
    $form['course_minutes'] = $request->get('course_minutes');
    $form['grade'] = $request->get('grade');
    $form['subject'] = $request->get('subject');
    if(empty($form['subject'])) $form['subject'] = '';
    $form['start_date'] = $request->get('start_date');
    $form['end_date'] = $request->get('end_date');
    $form['lesson_week_count'] = $request->get('lesson_week_count');
    return $form;
  }
  public function update_form(Request $request){
    return $this->create_form($request);
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
    $fields = [
      'student_name' => [
        'label' => '生徒',
        'size' => 6,
      ],
      'grade_name' => [
        'label' => '学年',
        'size' => 6,
      ],
      'lesson_name' => [
        'label' => 'レッスン',
        'size' => 6,
      ],
      'course_type_name' => [
        'label' => '授業形態',
        'size' => 6,
      ],
      'teacher_name' => [
        'label' => '講師',
        'size' => 6,
      ],
      'course_minutes_name' => [
        'label' => '授業時間',
        'size' => 6,
      ],
      'tuition_money' => [
        'label' => '受講料',
        'size' => 6,
      ],
      'remark' => [
        'label' => '備考',
      ],
    ];
    return view('components.page', [
      'fields'=>$fields])
      ->with($param);
  }
  /**
   * 新規登録ロジック
   *
   * @return \Illuminate\Http\Response
   */
  public function _store(Request $request)
  {
    $form = $this->create_form($request);
    $res = $this->transaction($request, function() use ($request, $form){
      $item = Tuition::add($form);
      return $this->api_response(200, '', '', $item);
    }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
   }
   public function _update(Request $request, $id)
   {
     $res =  $this->transaction($request, function() use ($request, $id){
       $item = Tuition::where('id',$id)->first();
       $item->change($this->update_form($request));
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function get_api_tuition(Request $request){
     $fields = ['lesson', 'course', 'course_minutes', 'lesson_week_count', 'grade', 'teacher_id'];
     foreach($fields as $field){
       if(!$request->has($field)){
         return $this->bad_request($field.' not found');
       }
     }
     $form = $request->all();
     if(!$request->has('is_juken')){
       $form['is_juken'] = 0;
     }
     $res = UserCalendarMemberSetting::get_api_lesson_fee($form['lesson'],
                                                         $form['course'],
                                                         $form['course_minutes'],
                                                         $form['lesson_week_count'],
                                                         $form['grade'],
                                                         $form['is_juken'],
                                                         $form['teacher_id']);
     if($res==null){
       return $this->error_response('api error');
     }
     return $res;
   }
}
