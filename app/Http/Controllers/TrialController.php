<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Trial;
use DB;
use View;
class TrialController extends UserCalendarController
{
  public $domain = "trials";
  public $table = "trials";
  public $domain_name = "体験授業申し込み";
  private $show_fields = [
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
      'hr' => true
    ],
    'subject1' => [
      'label' => '補習科目',
      'size' => 3,
    ],
    'subject2' => [
      'label' => '受験科目',
      'size' => 3,
    ],
    'lesson_place' => [
      'label' => '希望校舎',
      'size' => 3,
    ],
    'english_teacher' => [
      'label' => '英会話講師希望',
      'size' => 3
    ],
    'piano_level' => [
      'label' => 'ピアノ経験',
      'size' => 3
    ],
    'remark' => [
      'label' => '問い合わせ・質問',
      'size' => 9,
      'hr' => true,
    ],
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
  ];

  public function model(){
    return Trial::query();
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
    $user = $this->login_details();
    if(!isset($user)) {
      /*
      if(!empty($request->key)){
         abort(403);
      }
      $user = User::where('access_key',$request->key);
      if($user->count() < 1){
        abort(403);
      }
      $user = $user->first()->details();
      */
      abort(404);
    }

    if(!isset($user)) {
      abort(404);
    }
/*
    if($this->is_manager($user->role)!==true){
      abort(403);
    }
*/
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'search_word'=>$request->search_word,
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
    $items->with('student', 'parent');
    $user = $this->login_details();
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
          'label' => 'ステータス',
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
      if(!empty($status) && $status==='confirm'){
        $calendar = $item->get_calendar();
        if(isset($calendar)){
          $calendar = $calendar->details();
          $item->teacher_name = $calendar['teacher_name'];
          $item->datetime = $calendar['datetime'];
        }
      }
    }
    return ['items' => $items->toArray(), 'fields' => $fields];
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
      $items = $items->findStatuses("new");
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

    return view($this->domain.'.page', [
      'action' => $request->get('action'),
      'fields'=>$fields])
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
      'domain_name' => $this->domain_name,
      'attributes' => $this->attributes(),
    ];
    return view($this->domain.'.entry',
      ['sended' => ''])
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
     $user = User::where('email', $form['email'])->first();
     if(isset($user)){
       return view($this->domain.'.entry',
         ['result' => "aleready"]);
     }
     $res = $this->_trial_store($request);
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
     else {
       return $this->save_redirect($res, [], '', $this->domain.'/entry');
     }
   }
   public function _trial_store(Request $request)
   {
     $form = $request->all();

     try {
       DB::beginTransaction();
       $form["accesskey"] = '';
       $form["password"] = 'sakusaku';
       $parent = Trial::entry($form);
       DB::commit();
       return $this->api_response(200, __FUNCTION__);
     }
     catch (\Illuminate\Database\QueryException $e) {
       DB::rollBack();
       return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
     catch(\Exception $e){
       DB::rollBack();
       return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
   }
   /**
    * 体験申し込み更新ページ
    *
    * @param  int  $id
    * @param  string  $status
    * @return \Illuminate\Http\Response
    */
   public function status_update_page(Request $request, $id, $status)
   {
     if (!View::exists($this->domain.'.'.$status)) {
         abort(404);
     }
     $param = $this->get_param($request, $id);
     $param['fields'] = $this->show_fields;
     if($status === 'confirm'){
       //体験かつ、講師に授業予定を連絡するケース
       $param['candidate_teachers'] = $param['item']->candidate_teachers();
     }
     else if($status ==='fix'){
       //体験かつ、生徒が授業予定をfixするケース
       $param['trial_id'] = $param['item']->id;
       $calendar = $param['item']->get_calendar();
       if(!isset($calendar)){
         abort(404);
       }
       $param['item'] = $calendar->details();
       $param['fields'] = [
         'date' => [
           'label' => '日付',
           'size' => 6,
         ],
         'timezone' => [
           'label' => '時間',
           'size' => 6,
         ],
         'student_name' => [
           'label' => '生徒',
           'size' => 6,
         ],
         'teacher_name' => [
           'label' => '講師',
           'size' => 6,
         ],
         'place' => [
           'label' => '場所',
           'size' => 6,
         ]
       ];
     }
     if(!isset($param['item'])) abort(404);

     return view($this->domain.'.'.$status, [ ])->with($param);
   }
   /**
    * カレンダーステータス更新
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @param  string  $status
    * @return \Illuminate\Http\Response
    */
   public function status_update(Request $request, $id, $status)
   {
     $param = $this->get_param($request, $id);
     $item = $param['item'];
     $res = $this->api_response();
     $message = [
       "confirm" => "授業予定の確認連絡をしました。",
       "remind" => "授業予定の確認連絡をしました。",
       "fix" => "授業予定を確定しました。",
       "cancel" => "この授業予定をキャンセルしました。",
     ];
     if($status !== "remind"){
       //リマインドはステータス更新なし
       $res = $this->_status_update($request, $id, $status);
     }

     if($status==="remind" || $status==="cancel"){
       $calendar = $item->get_calendar();
       if(isset($calendar)){
         if($calendar->status!='new'){
           $url = config('app.url').'/calendars/'.$calendar->id.'/'.$status;
           $res = $this->call_api($request, $url, 'PUT');
           //TODO : errorハンドリング
           $res = $this->api_response();
         }
         else {
           $this->confirm_mail($calendar->details());
         }
       }
     }

     return $this->save_redirect($res, $param, $message[$status]);
   }
   /**
    * 体験授業予定連絡通知メール送信
    * @param  Array  $param
    * @return boolean
    */
   private function confirm_mail($calendar){
     return  $this->_mail($calendar,
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
   protected function _mail($calendar, $title, $template){
     $login_user = $this->login_details();
     $members = $calendar->members;
     foreach($members as $member){
       $email = $member->user['email'];
       $user = $member->user->details();
       $send_from = 'manager';
       $send_to = 'teacher';
       $user_name = $user['name'];
       if(!$this->is_teacher($user->role)) continue;

       $this->send_mail($email,
        $title,
        [
        'user_name' => $user_name,
        'send_from' => $send_from,
        'send_to' => $send_to,
        'item' => $calendar
        ],
        'text',
        $template);
     }
     return true;
   }

   /**
    * トライアルステータス更新
    *
    * @param  array  $param
    * @param  string  $status
    * @return \Illuminate\Http\Response
    */
   private function _status_update($request, $id, $status){
     try {
       DB::beginTransaction();
       $form = $request->all();
       $user = $this->login_details();
       $form['create_user_id'] = $user->user_id;
       //カレンダーステータス変更
       $trial = Trial::where('id', $id)->first();
       $trial->update(['status'=>$status]);
       if($status==="confirm"){
         $calendar = $trial->trial_to_calendar($form);
         $this->confirm_mail($calendar->details());
       }
       $this->send_slack('体験授業ステータス更新['.$status.']:/ id['.$trial->id.']開始日時['.$calendar['start_time'].']終了日時['.$calendar['end_time'].']生徒['.$calendar['student_name'].']講師['.$calendar['teacher_name'].']', 'info', '体験授業ステータス更新');
       DB::commit();
       return $this->api_response(200, '', '', $calendar);
     }
     catch (\Illuminate\Database\QueryException $e) {
         DB::rollBack();
         $this->send_slack('体験授業ステータス更新エラー:'.$e->getMessage(), 'error', '体験授業ステータス更新');
         return $this->error_response('Query Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
     catch(\Exception $e){
         DB::rollBack();
         $this->send_slack('体験授業ステータス更新エラー:'.$e->getMessage(), 'error', '体験授業ステータス更新');
         return $this->error_response('DB Exception', '['.__FILE__.']['.__FUNCTION__.'['.__LINE__.']'.'['.$e->getMessage().']');
     }
   }
}
