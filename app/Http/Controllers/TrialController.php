<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Trial;
use App\Models\Tuition;
use App\Models\UserCalendar;
use DB;
use View;
class TrialController extends UserCalendarController
{
  public $domain = "trials";
  public $table = "trials";

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
    'date1' => [
      'label' => '第１希望',
      'size' => 3,
    ],
    'date2' => [
      'label' => '第２希望',
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
    return Trial::query();
  }
  /**
   * 新規登録用フォーム
   *
   * @param  \Illuminate\Http\Request  $request
   * @return json
   */
  public function create_form(Request $request){
    $form = $request->all();
    if(!empty($form['trial_date1'])){
      $form['trial_start_time1'] = $form['trial_date1'].' '.$form['trial_start_time1'].':00:00';
      $form['trial_end_time1'] = $form['trial_date1'].' '.$form['trial_end_time1'].':00:00';
    }
    if(!empty($form['trial_date2'])){
      $form['trial_start_time2'] = $form['trial_date2'].' '.$form['trial_start_time2'].':00:00';
      $form['trial_end_time2'] = $form['trial_date2'].' '.$form['trial_end_time2'].':00:00';
    }
    if(!empty($form['trial_date3'])){
      $form['trial_start_time3'] = $form['trial_date3'].' '.$form['trial_start_time3'].':00:00';
      $form['trial_end_time3'] = $form['trial_date3'].' '.$form['trial_end_time3'].':00:00';
    }
    return $form;
  }

  /**
   * 一覧表示
   *
   * @param  \Illuminate\Http\Request  $request
   * @return view / domain.lists
   */
  public function index(Request $request)
  {
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
  /**
   * 共通パラメータ取得
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null, $user_id=null){
    $user = $this->login_details($request);
    if(!isset($user)) {
      abort(403);
    }

    if(!isset($user)) {
      abort(403);
    }
    $ret = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'search_word'=>$request->search_word,
      '_status' => $request->get('status'),
      'search_status'=>$request->status,
      'access_key'=>$request->key,
      'attributes' => $this->attributes(),
    ];
    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      if(!isset($item)){
        abort(404);
      }
      $ret['item'] = $item->details();
    }
    else {
      $lists = ['cancel', 'new', 'fix', 'confirm', 'complete', 'presence'];
      foreach($lists as $list){
        $_status = $list;
        $ret[$list.'_count'] = $this->model()->findStatuses($_status)->count();
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
  public function search(Request $request, $user_id=0)
  {
    $items = $this->model();
    $items->with('parent');
    $user = $this->login_details($request);
    $items = $this->_search_scope($request, $items);
    $items = $this->_search_pagenation($request, $items);

    $items = $this->_search_sort($request, $items);
    $items = $items->get();
    $status = $request->get('status');
    if(!empty($status) && $status==='confirm'){
      $fields = [
        'create_date' => [
          'label' => '申込年月日',
          'link' => 'show',
        ],
        'datetime' => [
          'label' => '授業予定',
        ],
        'teacher_name' => [
          'label' => '講師氏名',
        ],
        'student_name' => [
          'label' => '生徒氏名',
        ],
      ];
      $fields['buttons'] = [
        'label' => '・・・',
        'button' => [
          [ "label" => "授業予定リマインド",
            "method" => "remind",
            "style" => "danger",
          ]
        ]
      ];
    }
    else {
      $fields =[
        'create_date' => [
          'label' => '申込年月日',
          'link' => 'show',
        ],
        'status_name' => [
          'label' => __('labels.status'),
        ],
        'date1' => [
          'label' => '第１希望',
        ],
        'date2' => [
          'label' => '第２希望',
        ],
        'student_name' => [
          'label' => '生徒氏名',
        ],
        /*
        'parent_name' => [
          'label' => '顧客氏名',
        ],
        'grade' => [
          'label' => '学年',
        ],
        'subject1' => [
          'label' => '補習科目',
        ],
        'subject2' => [
          'label' => '受験科目',
        ],
        */
      ];
      /*
          $fields['buttons'] = [
            'label' => '操作',
            'button' => ['edit', 'delete']
          ];
      */
      $fields['buttons'] = [
        'label' => '・・・',
        'button' => [
          [ "label" => "体験授業予定登録",
            "method" => "confirm",
            "style" => "primary",
          ]
        ]
      ];
    }
    foreach($items as $item){
      $item = $item->details();
    }
    return ['items' => $items, 'fields' => $fields];
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
    if(isset($request->status)){
      $items = $items->findStatuses($request->status);
    }
    else {
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
      'howto' => [
        'label' => '当塾をお知りになった方法は何でしょうか？',
        'size' => 6
      ],
      'howto_word' => [
        'label' => '検索ワードを教えてください',
        'size' => 6
      ],
    ]);
    $param['view'] = 'page';
    return view($this->domain.'.'.$param['view'], [
      'action' => $request->get('action'),
      'fields'=>$fields])
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
    return view($this->domain.'.edit', [
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
         abort(403, 'このメールアドレスは登録済みです。');
       }
     }
     $res = $this->transaction($request, function() use ($request){
       $form = $this->create_form($request);
       $form['create_user_id'] = 1;
       $form["accesskey"] = '';
       $form["password"] = 'sakusaku';
       if(!empty($form['student2_name_last'])){
         $form['course_type'] = 'family';
       }
       $item = Trial::entry($form);
       return $this->api_response(200, '', '', $item);
     }, '体験授業申込', __FILE__, __FUNCTION__, __LINE__ );

     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         '体験授業のお申込み、ありがとうございます', [
         'user_name' => $form['student_name_last'].' '.$form['student_name_first'],
         'access_key' => $access_key,
         'send_to' => 'parent',
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
       $trial = Trial::where('id', $id)->first();
       $res = $trial->trial_to_calendar($form);
       return $res;
     }, '体験授業ステータス更新', __FILE__, __FUNCTION__, __LINE__ );
     if($this->is_success_response($res)){
       $this->confirm_mail($param, $res["data"]->details($user->user_id));
     }
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

       $this->send_mail($email,
        $title,
        [
        'login_user' => $login_user,
        'user_name' => $user_name,
        'send_from' => $send_from,
        'send_to' => $send_to,
        'item' => $calendar
        ],
        'text',
        $template,
        $member->user->get_locale()
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
      $trial = Trial::where('id', $id)->first();
      $setting = $trial->to_calendar_setting($form, $form['calendar_id']);
      return $this->api_response(200, '', '', $setting);
    }, '通常授業予定設定', __FILE__, __FUNCTION__, __LINE__ );
    if($res["data"]==null){
      $res = $this->error_response("この設定と競合する設定があり、登録できませんでした。");
    }

    return $this->save_redirect($res, [], '通常授業予定を設定しました。', '/trials/'.$id.'');
  }

   public function admission(Request $request, $id){
     $access_key = '';
     $trial = Trial::where('id', $id)->first();
     if(!isset($trial)) abort(404);

     $param = [
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];
     return view($this->domain.'.admission',
       ['sended' => '',
        '_edit' => false])
       ->with($param);

   }
   public function ask_candidate(Request $request, $id){
     $trial = Trial::where('id', $id)->first();
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
   public function ask_candidate_mail(Request $request, $id){
     $access_key = $this->create_token(2678400);
     $param = $this->get_param($request, $id);
     $access_key = $this->create_token();

     $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){

       return $this->api_response(200, '', '', []);
     }, '体験授業候補日連絡メール送信', __FILE__, __FUNCTION__, __LINE__ );
     return $this->save_redirect($res, [], '体験授業候補日連絡メールを送信しました。');
   }

   public function add_candidate_date(Request $request, $id){
     $trial = Trial::where('id', $id)->first();
     if(!isset($trial)) abort(404);

     $param = [
       'item' => $trial->details(),
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
     ];
     return view($this->domain.'.add_candidate_date',
       ['sended' => '',
        '_edit' => false])
       ->with($param);

   }
   public function add_candidate_date_send(Request $request, $id){
     $access_key = $this->create_token(2678400);
     $param = $this->get_param($request, $id);
     $access_key = $this->create_token();

     $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){

       return $this->api_response(200, '', '', []);
     }, '体験授業候補日連絡メール送信', __FILE__, __FUNCTION__, __LINE__ );
     return $this->save_redirect($res, [], '体験授業候補日連絡メールを送信しました。');
   }

   public function _update(Request $request, $id)
   {
     $res =  $this->transaction($request, function() use ($request, $id){
       $form = $this->create_form($request);
       $user = $this->login_details($request);
       $form['create_user_id'] = $user->user_id;
       $item = Trial::where('id', $id)->first();
       $item->trial_update($form);
       return $this->api_response(200, '', '', $item);
     }, '更新しました。', __FILE__, __FUNCTION__, __LINE__ );
     return $res;
   }
   public function admission_mail(Request $request, $id){
     $access_key = '';
     $trial = Trial::where('id', $id)->first();
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
    $res = $this->transaction($request, function() use ($request, $id, $param, $access_key){
      $trial = Trial::where('id', $id)->first();
      $ask = $trial->agreement_ask($param['user']->user_id, $access_key);
      foreach($trial->trial_students as $s){
        //受講料delete-insert
        Tuition::where('student_id' , $s->student_id)->delete();
        foreach($s->student->user->calendar_setting() as $schedule_method => $d1){
            foreach($d1 as $lesson_week => $settings){
              foreach($settings as $setting){
                $setting = $setting->details();
                $subject = '';
                if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true){
                  $subject= $setting->get_tag_value('subject');
                }
                elseif($setting->get_tag_value('lesson')==4){
                  $subject= $setting->get_tag_value('kids_lesson');
                }
                if(empty($request->get($setting->id.'_tuition'))){
                  continue;
                }
                Tuition::add([
                  'student_id' => $s->student_id,
                  'teacher_id' => $setting->user->details()->id,
                  'tuition' => $request->get($setting->id.'_tuition'),
                  'title' => $setting['title'],
                  'remark' => '',
                  "lesson" => $setting->get_tag_value('lesson'),
                  "course_type" => $setting->get_tag_value('course_type'),
                  "course_minutes" => $setting->course_minutes,
                  "grade" => $s->student->tag_value('grade'),
                  "lesson_week_count" => $s->student->tag_value('lesson_week_count'),
                  "subject" => $subject,
                  "create_user_id" => $param['user']->user_id,
                  "start_date" => '9999-12-31',
                  "end_date" => '9999-12-31',
                ]);
              }
            }
          }
      }
      //受講料初期設定
      return $this->api_response(200, '', '', $ask);
    }, '入会案内連絡', __FILE__, __FUNCTION__, __LINE__ );
    return $this->save_redirect($res, [], '入会案内メールを送信しました。');
  }
}
