<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\LessonRequest;
use App\Models\Tuition;
use App\Models\StudentParent;
use App\Models\Student;
use App\Models\Event;
use App\Models\EventUser;
use App\Models\UserCalendar;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;

use DB;
use View;
class LessonRequestController extends UserCalendarController
{
  public $domain = "lesson_requests";
  public $table = "lesson_requests";

  private $show_fields = [
    'student_name' => [
      'label' => '生徒氏名',
      'size' => 3
    ],
    'student_gender' => [
      'label' => '性別',
      'size' => 3,
    ],
    'grade' => [
      'label' => '学年',
      'size' => 3,
    ],
    'school_name' => [
      'label' => '学校',
      'size' => 3,
      'hr' => true
    ],
    'create_date' => [
      'label' => '申込年月日',
      'size' => 3
    ],
    'status_name' => [
      'label' => 'ステータス',
      'size' => 3,
    ],
    'lesson' => [
      'label' => '希望レッスン',
      'size' => 3,
    ],
    'lesson_place' => [
      'label' => '希望校舎',
      'size' => 3,
    ],
    'course_minutes' => [
      'label' => '希望授業時間',
      'size' => 3
    ],
    'english_talk_course_type' => [
      'label' => '英会話授業形式',
      'size' => 3,
      'hr' => true
    ],
    'subject1' => [
      'label' => '補習科目',
      'size' => 6,
    ],
    'subject2' => [
      'label' => '受験科目',
      'size' => 6,
    ],
    'english_teacher' => [
      'label' => '講師希望',
      'size' => 3
    ],
    'english_talk_lesson' => [
      'label' => '英会話レッスン',
      'size' => 3
    ],
    'piano_level' => [
      'label' => 'ピアノ経験',
      'size' => 3
    ],
    'kids_lesson' => [
      'label' => '習い事',
      'size' => 3
    ],
    'kids_lesson_course_type' => [
      'label' => '習い事授業形式',
      'size' => 3,
      'hr' => true
    ],
    'remark' => [
      'label' => '問い合わせ・質問',
      'size' => 9,
      'hr' => true,
    ],
  ];

  public function model(){
    return LessonRequest::query();
  }
  /**
   * 新規登録用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
   public function create_form(Request $request){
     $form = $request->all();
     $form['request_dates'] = [];
     $i = 0;
     if(!empty($form['trial_date1'])){
       $form['request_dates'][] = [
         "from_datetime" => $form['trial_date1'].' '.$form['trial_start_time1'].':00:00',
         "to_datetime" => $form['trial_date1'].' '.$form['trial_end_time1'].':00:00',
         "sort_no" => $i++
       ];
     }
     if(!empty($form['trial_date2'])){
       $form['request_dates'][] = [
         "from_datetime" => $form['trial_date2'].' '.$form['trial_start_time2'].':00:00',
         "to_datetime" => $form['trial_date2'].' '.$form['trial_end_time2'].':00:00',
         "sort_no" => $i++
       ];
     }
     if(!empty($form['trial_date3'])){
       $form['request_dates'][] = [
         "from_datetime" => $form['trial_date3'].' '.$form['trial_start_time3'].':00:00',
         "to_datetime" => $form['trial_date3'].' '.$form['trial_end_time3'].':00:00',
         "sort_no" => $i++
       ];
     }
     if(!empty($form['hope_datetime'])){
       foreach($form['hope_datetime'] as $datetime){
         if(empty($datetime)) continue;
         $d = explode(' ', $datetime);
         $date = $d[0];
         if(!isset($form['hope_'.strtotime($date).'_date']) || $form['hope_'.strtotime($date).'_date']!='true') continue;
         $timezone = trim($d[1]);
         $t = explode('-', $timezone);
         $form['request_dates'][$date] = [
           "from_datetime" => $date.' '.$t[0].':00:00',
           "to_datetime" => $date.' '.$t[1].':00:00',
           "sort_no" => 1
         ];
       }
     }
     $user = $this->login_details($request);
     if(isset($user)){
       $form['create_user_id'] = $user->user_id;
     }
     else if($request->has('event_user_id')>0 && $request->has('access_key')){
       $event_user = EventUser::where('id', $request->get('event_user_id'))
                  ->where('access_key', $request->get('access_key'))
                  ->first();
       $form['create_user_id'] = $event_user->user_id;
     }
     return $form;
   }

  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request, $event_id=0)
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
    if($event_id>0){
      $request->merge([
        'event_id' => $event_id,
      ]);
    }


    $param = $this->get_param($request);
    $event = null;
    if($event_id>0){
      $event = Event::where('id', $event_id)->first();
    }
    else {
      abort(404);
    }
    $user = $param['user'];
    if(isset($user) && !$this->is_manager($user->role)){
      //事務以外 一覧表示は不可能
      abort(403);
    }

    $_table = $this->search($request, $event_id);
    if(!isset($event)) abort(404);
    $param['event_id'] = $event_id;
    $param['event'] = $event;
    return view($this->domain.'.lists', $_table)
      ->with($param);
  }
  /**
   * 共通パラメータ取得
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null, $user_id=null){
    $ret = $this->get_common_param($request, false);
    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id',$id)->first();
      if(!isset($item)){
        abort(404);
      }
      if($request->has('event_user_id')){
        //access_keyとevent_user_idを持つ場合
        $event_user = EventUser::where('id', $request->get('event_user_id'))->first();
        if($event_user->event_id != $item->event_id) abort(404);
        if(isset($ret['user'])){
          if(!(
            ($this->is_parent($ret['user']->role)==true && $ret['user']->user_id == $item->create_user_id) ||
              $this->is_manager($ret['user']->role)==true)){
            //ログインユーザーが起票者ではない
            //事務権限ではない場合
            abort(404);
          }
        }
        else if($event_user->acces_key!=$event_user->access_key) {
          abort(404);
        }
      }

      $ret['item'] = $item;
    }
    else {
      if(isset($ret['user']) && $this->is_manager($ret['user']->role)!=true){
        abort(403);
      }
      $lists = ['cancel', 'new', 'fix', 'confirm', 'schedule_commit',  'complete', 'presence', 'entry_contact', 'entry_hope', 'entry_guidanced', 'entry_cancel'];
      foreach($lists as $list){
        $_status = $list;
        if($request->has('event_id')){
          $ret[$list.'_count'] = $this->model()->where('event_id', $request->get('event_id'))->findStatuses($_status)->count();
        }
      }
    }
    return $ret;
  }

  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search(Request $request, $event_id=0)
  {
    $user = $this->login_details($request);
    $items = $this->model()->where('type', 'season_lesson');
    $items = $items->where('event_id', $event_id);
    $items->with('parent');
    $items = $this->_search_scope($request, $items);
    $count = $items->count();

    $request->merge([
      '_sort' => 'created_at'
    ]);
    if($request->get('is_desc')==1){
      $request->merge([
        '_sort_order' => 'desc',
      ]);
    }
    $items = $this->_search_sort($request, $items);
    //$items = $items->paginate($request->get('_line'));
    return ['items' => $items->get(), 'count' => $count];
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
    //ステータス 検索
    if($request->has('search_status')){
      $items = $items->findStatuses($request->search_status);
    }
    else {
      if($request->has('list')){
        $items = $items->findStatuses($request->list);
      }

    }
    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }

    return $items;
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

    $fields = array_merge($this->show_fields, [
      'parent_email' => [
        'label' => 'email',
        'size' => 6
      ],
      /*
      'parent_phone_no' => [
        'label' => 'ご連絡先',
        'size' => 3,
      ],
      */
      'entry_milestone' => [
        'label' => __('labels.entry_milestone'),
        'size' => 6
      ],
      'entry_milestone_word' => [
        'label' => 'やって欲しいこと（その他）',
        'size' => 6
      ],
      'howto' => [
        'label' => '当塾をお知りになった方法は何でしょうか？',
        'size' => 6
      ],
      'howto_word' => [
        'label' => '検索ワードを教えてください',
        'size' => 6
      ],
    ]);
    $param = $this->get_param($request, $id);
    $view = $this->domain.".".$param['item']->type.'.page';
    switch($param['item']->type){
      case "trial":
        $view = 'trials.page';
        break;
      case "season_lesson":
      case "season_lesson_teacher":
        $param['event'] = $param['item']->event;
        $param['event_dates'] = $param['item']->event->get_event_dates();
        break;
    }
    return view($view, [
      'action' => $request->get('action'),
      'fields'=>$fields, '_edit' => false])
      ->with($param);
  }
  public function show_dialog(Request $request, $id)
  {
    if(!$request->has('student_parent_id')) abort(403);
    $param = $this->get_common_param($request);
    $item = LessonRequest::where('id', $id)->first();
    if(!isset($item) || $item->student_parent_id != $request->has('student_parent_id'))abort(403);
    $param['item'] = $item->details();
    return view('trials.dialog', [])
      ->with($param);
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
    switch($param['item']->type){
      case "trial":
        $view = 'trials.edit';
        break;
      case "season_lesson":
      case "season_lesson_teacher":
        $view = 'lesson_requests.'.$param['item']->type.'.edit';
        $param['event'] = $param['item']->event;
        $param['access_key'] = $request->get('access_key');
        $param['event_user_id'] = $request->get('event_user_id');
        $param['event_dates'] = $param['item']->event->get_event_dates();
        break;
    }
    return view($view, [
      '_edit' => true])
      ->with($param);
  }
  /**
   * 体験授業申し込みページ
   *
   * @return \Illuminate\Http\Response
   */
  public function trial(Request $request)
  {
    $param = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'attributes' => $this->attributes(),
      'item' => [],
    ];
    return view($this->domain.'.entry',
      ['sended' => '',
       '_edit' => false])
      ->with($param);
   }
   public function store(Request $request)
   {
     $form = $request->all();
     if(!$request->has('event_user_id')) abort(403);
     $event_user = EventUser::where('id', $request->get('event_user_id'))->first();
     if(!isset($event_user)) abort(403);

     $param = $this->get_param($request);
     $form = $this->create_form($request);
     $form['access_key'] = $this->create_token();

     $res = $this->transaction($request, function() use ($form){
       $item = LessonRequest::add($form);
       return $this->api_response(200, '', '', $item);
     }, '体験授業申込', __FILE__, __FUNCTION__, __LINE__ );

     if($this->is_success_response($res)){
       $mail_title = [
         'trial' => '授業のお申込み、ありがとうございます',
         'season_lesson' => '授業のお申込み、ありがとうございます',
         'season_lesson_teacher' => '講習の勤務可能日時を登録しました',
       ];
       $event_user->user->send_mail(
         $mail_title[$form['type']], [
         'user_name' => $event_user->user->get_name(),
         'event_user_id' => $request->get('event_user_id'),
         'domain' => $request->get('domain'),
         'domain_item_id' => $request->get('domain_item_id'),
         'access_key' => $form['access_key'],
         'item' => $res['data'],
         'send_to' => $event_user->user->get_role(),
       ], 'text', $form['type']);
     }
     return $this->save_redirect($res, [], '');
   }
   /**
    * 体験授業申し込みページ
    *
    * @return \Illuminate\Http\Response
    */
   public function trial_store(Request $request)
   {
     $access_key = $this->create_token();
     $request->merge([
       'access_key' => $access_key,
     ]);
     $form = $request->all();

     $u = User::where('email', $form['email'])->first();
     if(isset($u)){
       if($u->details()->role != 'parent'){
         abort(403, __('messages.message_already_email'));
       }
     }
     $res = $this->transaction($request, function() use ($request){
       $form = $this->create_form($request);
       $form["event_id"] = 0;
       $form["accesskey"] = '';
       $form["password"] = 'sakusaku';
       $item = LessonRequest::entry($form);
       return $this->api_response(200, '', '', $item);
     }, '体験授業申込', __FILE__, __FUNCTION__, __LINE__ );

     if($this->is_success_response($res)){
       $u = $res['data']->parent->user;
       $this->send_mail($form['email'],
         '体験授業のお申込み、ありがとうございます', [
         'user_name' => $form['parent_name_last'].' '.$form['parent_name_first'],
         'access_key' => $access_key,
         'item' => $res['data'],
         'send_to' => 'parent',
         'login_user' => $u->details(),
       ], 'text', 'trial');
       return view($this->domain.'.entry',
         ['result' => "success"]);
     }
     return $this->save_redirect($res, [], '', $this->domain.'/entry');
   }
   /**
    * 詳細画面表示
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function to_calendar(Request $request, $id)
   {
     set_time_limit(1200);
     $param = $this->get_param($request, $id);

     $teacher_id = 0;
     $lesson = 0;
     if($request->has('teacher_id')){
       $teacher_id = $request->get('teacher_id');
     }
     if($request->has('lesson')){
       $lesson = $request->get('lesson');
     }
     $param['candidate_teachers'] = $param['item']->candidate_teachers($teacher_id, $lesson);
     $param['view'] = 'to_calendar';
     $param['select_teacher_id'] = $teacher_id;
     $param['select_lesson'] = $lesson;
     return view($this->domain.'.'.$param['view'], [])
       ->with($param);
   }
   /**
    * カレンダーステータス更新
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @param  string  $status
    * @return \Illuminate\Http\Response
    */
   public function to_calendar_confirm(Request $request, $id)
   {
     $form = $request->all();
     $param = $this->get_param($request, $id);
     $item = $param['item'];
     $user = $this->login_details($request);
     $res = $this->transaction($request, function() use ($request, $id, $user){
       $form = $request->all();
       $form['create_user_id'] = $user->user_id;
       //カレンダーステータス変更
       $trial = LessonRequest::where('id', $id)->first();
       $res = $trial->request_to_calendar($form);
       return $res;
     }, '体験授業ステータス更新', __FILE__, __FUNCTION__, __LINE__ );
     return $this->save_redirect($res, $param, "授業予定の確認連絡をしました。", $this->domain.'/'.$id);
   }
   /**
    * 体験授業予定連絡通知メール送信
    * @param  Array  $param
    * @return boolean
    */
   private function confirm_mail($param, $calendar){
     return  $this->trial_mail($param, $calendar,
              '体験授業予定のご確認',
              'trial_confirm');
   }
   /**
    * 予定確認メール送信
    * カレンダーの対象者に確認メールを送信する
    * send_type: 生徒あて=student | 講師あて=teacher
    * template : emails\[template)_text.blade.phpを指定する
    * @param  \Illuminate\Http\Request  $request
    * @param  string  $title
    * @param  string  $template
    * @return view
    */
   protected function trial_mail($param, $calendar, $title, $template){
     $login_user = $param['user'];
     $members = $calendar->members;

     foreach($members as $member){
       $email = $member->user['email'];
       $user = $member->user->details();
       $send_from = 'manager';
       $send_to = 'teacher';
       $user_name = $user['name'];
       //講師以外には送らない
       if(!$this->is_teacher($user->role)) continue;
       $member->user->send_mail(
         $title,
         [
         'login_user' => $login_user,
         'user_name' => $user_name,
         'send_from' => $send_from,
         'send_to' => $send_to,
         'item' => $calendar
         ],
        'text',
        $template
       );
     }
     return true;
   }

   /**
    * 詳細画面表示
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function to_calendar_setting(Request $request, $id)
   {
     $param = $this->get_param($request, $id);
     $teacher_id = 0;
     $calendar_id = 0;
     $lesson = 0;
     $param['calendar'] = null;
     if($request->has('calendar_id')){
       $calendar_id = $request->get('calendar_id');
       $calendar = UserCalendar::where('id', $calendar_id)->first();
       if(!isset($calendar)) abort(404);
       $calendar = $calendar->details($param['user']->user_id);
       $teacher_id = $calendar['teachers'][0]->user->teacher->id;
       $lesson = $calendar->get_tag('lesson')->tag_value;
       $param['calendar'] = $calendar;
     }
     if($teacher_id > 0 && $lesson > 0){
      $candidate_teachers  = $param['item']->candidate_teachers($teacher_id, $lesson);
      $param['candidate_teacher'] = $candidate_teachers[0];
     }
     $param['view'] = 'to_calendar_setting';
     $param['select_teacher_id'] = $teacher_id;
     $param['select_lesson'] = $lesson;
     $param['select_calendar_id'] = $calendar_id;
     return view($this->domain.'.'.$param['view'], [])
       ->with($param);
   }
   /**
    * 体験授業予定連絡通知メール送信
    * @param  Array  $param
    * @return boolean
    */
  public function to_calendar_setting_update(Request $request, $id){
    $res =  $this->transaction($request, function() use ($request, $id){
      $form = $request->all();
      $user = $this->login_details($request);
      $form['create_user_id'] = $user->user_id;
      //カレンダーステータス変更
      $trial = LessonRequest::where('id', $id)->first();
      $res = $trial->to_calendar_setting($form, $form['calendar_id']);
      return $res;
    }, '通常授業予定設定', __FILE__, __FUNCTION__, __LINE__ );
    return $this->save_redirect($res, [], '通常授業予定を設定しました。', '/trials/'.$id.'');
  }

   public function ask_hope_to_join(Request $request, $id){
     $trial = LessonRequest::where('id', $id)->first();
     if(!isset($trial)) abort(404);

     $param = [
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];
     return view($this->domain.'.ask_hope_to_join',
       ['sended' => '',
        '_edit' => false])
       ->with($param);

   }
   public function ask_hope_to_join_mail_send(Request $request, $id){
     $param = $this->get_param($request, $id);
     $access_key = $this->create_token(2678400);

     $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){
       $param['item']->hope_to_join_ask($param['user']->user_id, $access_key);
       return $this->api_response(200, '', '', []);
     }, '入会希望受け取り連絡送信', __FILE__, __FUNCTION__, __LINE__ );
     return $this->save_redirect($res, [], '入会希望受け取り連絡を送信しました。');
   }

   public function ask_candidate(Request $request, $id){
     $trial = LessonRequest::where('id', $id)->first();
     if(!isset($trial)) abort(404);

     $param = [
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];
     return view($this->domain.'.ask_candidate',
       ['sended' => '',
        '_edit' => false])
       ->with($param);

   }
   public function ask_candidate_mail_send (Request $request, $id){
     $access_key = $this->create_token(2678400);
     $param = $this->get_param($request, $id);
     $param['access_key'] = $access_key;

     $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){
       LessonRequest::where('id', $id)->update(['status' => 'reapply']);
       $p = StudentParent::where('id', $param['item']->student_parent_id)->first();
       $p->user->update(['access_key' => $access_key]);
       return $this->api_response(200, '', '', []);
     }, '体験授業希望日変更願い', __FILE__, __FUNCTION__, __LINE__ );
     if($this->is_success_response($res)){
       $template = 'trial_candidate_date';
       $type = 'text';
       $param['user_name'] = $param['item']->parent->name();
       if($request->has('add_message')) $param['add_message'] = $request->get('add_message');
       $param['item']->parent->user->send_mail('体験授業希望日時について変更をお願いいたします', $param, $type ,$template);
     }
     return $this->save_redirect($res, [], '体験授業希望日変更願いメールを送信しました。');
   }

   public function candidate_date_edit(Request $request, $id){
     $trial = LessonRequest::where('id', $id)->first();
     if(!isset($trial)) abort(404);
     if(!$request->has('key')) abort(404);
     $access_key = $request->key;
     if(!$this->is_enable_token($access_key)){
       abort(403);
     }
     $user = User::where('access_key',$access_key)->first();
     if(!isset($user)){
       abort(403);
     }
     $param = [
       'user' => $user,
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];
     return view($this->domain.'.candidate_date',
       ['sended' => '',
        'access_key' => $access_key,
        '_edit' => false])
       ->with($param);

   }
   public function candidate_date_update(Request $request, $id){

     $access_key = $this->create_token(2678400);
     $param = $this->get_common_param($request, false);
     $param['item'] = LessonRequest::where('id', $id)->first();
     $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){
       $form = $this->create_form($request);
       $param['item']->update([
         'status' => 'new',
         'trial_start_time1' => $form['trial_start_time1'],
         'trial_end_time1' => $form['trial_end_time1'],
         'trial_start_time2' => $form['trial_start_time2'],
         'trial_end_time2' => $form['trial_end_time2'],
         'trial_start_time3' => $form['trial_start_time3'],
         'trial_end_time3' => $form['trial_end_time3'],
       ]);
       $p = StudentParent::where('id', $param['item']->student_parent_id)->first();
       //ログインしない場合、更新後表示できなくなる
       //$p->user->update(['access_key' => '']);
       return $this->api_response(200, '', '', []);
     }, '体験授業候補日連絡メール送信', __FILE__, __FUNCTION__, __LINE__ );

     if($this->is_success_response($res)){
       $template = 'candidate_date_update';
       $type = 'text';
       $param['user_name'] = $param['item']->parent->name();
       $param['item']->parent->user->send_mail('体験授業希望日を変更しました', $param, $type ,$template);
     }

     return $this->save_redirect($res, [], '体験授業希望日を変更しました');
   }
   public function update(Request $request, $id)
   {
     $param = $this->get_param($request, $id);
     $old_item = $param['item']->replicate();
     $old_item->id = $id;

     $res = $this->_update($request, $id);
     if($this->is_success_response($res) && !(isset($param['user']) && $param['user']->role=='manager')){
     }
     $mail_title = [
       'trial' => '授業のお申込み内容の変更を承りました',
       'season_lesson' => '授業のお申込み内容の変更を承りました',
       'season_lesson_teacher' => '講習の勤務可能日時を変更しました',
     ];
     $event_user_id = 0;
     $access_key = "";
     if($param['item']->event_id > 0){
       //event派生のlessonRequestの場合
       $event_user = EventUser::where('event_id', $param['item']->event_id)
                              ->where('user_id', $param['item']->user_id)
                              ->first();
       $event_user_id = $event_user->id;
       $access_key = $event_user->access_key;
     }
     $domain_item_id = $param['item']->user->details()->id;
     $param['item']->user->send_mail(
       $mail_title[$param['item']->type], [
       'user_name' => $param['item']->user->get_name(),
       'event_user_id' => $event_user_id,
       'domain' => $request->get('domain'),
       'domain_item_id' => $domain_item_id,
       'access_key' => $access_key,
       'item' => $res['data'],
       'old_item' => $old_item,
       'send_to' => $param['item']->user->get_role(),
     ], 'text', $param['item']->type.'_update');

     return $this->save_redirect($res, $param, '更新しました。');
   }
   public function _update(Request $request, $id)
   {
       $form = $this->create_form($request);
       $item = LessonRequest::where('id', $id)->first();
       $item->change($form);
       if($item->status=='reapply'){
         $item->update(['status' => 'confirm']);
       }
       return $this->api_response(200, '', '', $item);
       $res =  $this->transaction($request, function() use ($request, $id){
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function admission_mail(Request $request, $id){
     $access_key = '';
     $trial = LessonRequest::where('id', $id)->first();
     if(!isset($trial)) abort(404);
     $param = [
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];

     return view($this->domain.'.admission_mail',
       ['sended' => '',
        '_edit' => false])
       ->with($param);
   }
  public function admission_mail_send(Request $request, $id){
    $param = $this->get_param($request, $id);
    $access_key = $this->create_token(2678400);
    $res = $this->transaction($request, function() use ($request, $id){

      $trial = LessonRequest::where('id', $id)->first();
      //受講料初期設定
      foreach($trial->get_calendar_settings() as $setting){
        if($request->has($setting->id.'_tuition')){
          $member = UserCalendarMemberSetting::where('user_calendar_setting_id', $setting->id)->where('user_id', $trial->student->user_id)->first();
          $member->set_api_lesson_fee(intval($request->get($setting->id.'_tuition')));
        }
      }
      return $this->api_response(200, '', '', $trial);
    }, '入会案内連絡', __FILE__, __FUNCTION__, __LINE__ );
    if($this->is_success_response($res)){
      $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){
        $trial = LessonRequest::where('id', $id)->first();
        $ask = $trial->agreement_ask($param['user']->user_id, $access_key);
        return $this->api_response(200, '', '', $ask);
      }, '入会案内連絡', __FILE__, __FUNCTION__, __LINE__ );
    }
    return $this->save_redirect($res, [], '入会案内メールを送信しました。');
  }
  public function show_cancel_page(Request $request, $id){
    $param = $this->get_param($request,$id);
    $param['action'] = 'cancel';
    return view('components.confirm')->with($param);
  }
  public function cancel(Request $request,$id){
    $param = $this->get_param($request,$id);
    $res = $this->transaction($request, function() use ($request, $id, $param){
      $trial = $this->model()->where('id', $id)->first();
      $trial->update(['status'=>'cancel']);
      return $this->api_response(200, '', '', $trial);
    }, __('labels.trial_lesson').__('labels.info_deleted'), __FILE__, __FUNCTION__, __LINE__ );

    return $this->save_redirect($res,$param,__('messages.info_deleted'));
  }
  public function save_matching(Request $request,$event_id){
    set_time_limit(3600);

    $param = $this->get_param($request);
    $event = Event::find($event_id);
    if(!isset($event)) abort(404);
    $param['action'] = 'matching';
    $param['item'] = $event;
    $param['event_id'] = $event_id;
      $event = Event::find($event_id);
      $event->add_matching_calendar($request->get('selected_lesson_request_ids'));
    $res = $this->transaction($request, function() use ($request, $event_id, $param){
      return $this->api_response(200, '', '', null);
    }, '', __FILE__, __FUNCTION__, __LINE__ );
    abort(500, "マッチング処理不具合");

    if($this->is_success_response($res)){
      return $this->save_redirect($res,$param, '', "/events/".$event_id."/schedules");
    }
    abort(500, "マッチング処理不具合");
  }
  public function _delete(Request $request, $id)
  {
    $res = $this->transaction($request, function() use ($request, $id){
      $param = $this->get_param($request, $id);
      $user = $this->login_details($request);
      $param["item"]->update(['status'=>'cancel']);
      return $this->api_response(200, '', '', $param["item"]);
    }, '申し込みをキャンセルにしました', __FILE__, __FUNCTION__, __LINE__ );
    return $res;
  }
  public function estimate_page(Request $request, $id){
    $param = $this->get_param($request,$id);
    $param['calendars'] = $param['item']->lesson_request_calendars()
        ->where('status', 'complete')
        ->orderBy('start_time')->get();
    $view = 'lesson_requests.season_lesson.estimate_tuition';
    if($request->has('tuition')){
      $view = 'lesson_requests.season_lesson.estimate';
      $param['tuition'] = $request->get('tuition');
    }
    if($request->has('tuition')){
      $param['training_tuition'] = $request->get('training_tuition');
    }
    return view($view)->with($param);
  }
  public function save_estimate(Request $request,$id){
    $param = $this->get_param($request,$id);
    $res = $this->transaction($request, function() use ($request, $id, $param){
      $trial = $this->model()->where('id', $id)->first();
      $trial->update(['status'=>'cancel']);
      return $this->api_response(200, '', '', $trial);
    }, __('labels.trial_lesson').__('labels.info_deleted'), __FILE__, __FUNCTION__, __LINE__ );

    return $this->save_redirect($res,$param,__('messages.info_deleted'));
  }

}
