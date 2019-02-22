<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Trial;
use DB;
use View;
class TrialController extends MilestoneController
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
    'remark' => [
      'label' => '問い合わせ・質問',
      'size' => 3,
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
  public function get_param(Request $request, $id=null){
    $user = $this->login_details();
    if(!isset($user)) {
      abort(403);
    }
    if($this->is_manager($user->role)!==true){
      abort(403);
    }
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'origin' => $request->origin,
      'item_id' => $request->item_id,
      'search_word'=>$request->search_word,
      'search_status'=>$request->status,
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
  public function search(Request $request)
  {
    $items = $this->model();
    $user = $this->login_details();
    $items = $this->_search_scope($request, $items);
    $items = $this->_search_pagenation($request, $items);

    $items = $this->_search_sort($request, $items);
    $items = $items->get();
    $fields = [
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
      'parent_name' => [
        'label' => '顧客氏名',
      ],
      /*
      'student_name' => [
        'label' => '生徒氏名',
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
    foreach($items as $item){
      $item = $item->details();
    }
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
          "method" => "new",
          "style" => "primary",
        ]
      ]
    ];
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
      'parent_name' => [
        'label' => '顧客氏名',
        'size' => 3
      ],
      'parent_email' => [
        'label' => 'email',
        'size' => 3
      ],
      'parent_phone_no' => [
        'label' => 'ご連絡先',
        'size' => 3,
      ],
      'howto' => [
        'label' => '当塾をお知りになった方法は何でしょうか？',
        'size' => 3
      ],
      'howto_word' => [
        'label' => '検索ワードを教えてください',
        'size' => 3
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
     $result = '';
     $access_key = $this->create_token();
     $request->merge([
       'access_key' => $access_key,
     ]);
     $form = $request->all();
     $result = 'success';
     $res = $this->_trial_store($request);
     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         '体験授業のお申込み、ありがとうございます', [
         'user_name' => $form['parent_name_last'].' '.$form['parent_name_first'],
         'access_key' => $access_key,
         'send_to' => 'parent',
       ], 'text', 'entry');
       return view($this->domain.'.entry',
         ['result' => $result]);
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
     if($status === 'new'){
       $param['candidate_teachers'] = $param['item']->candidate_teachers();
     }
     $param['fields'] = $this->show_fields;
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
     $res = $this->_status_update($param, $status);
     $slack_type = 'error';
     $slack_message = '更新エラー';

     $status_update_message = [
       'fix' => '授業予定に出席予定と確認しました。',
       'confirm' => '授業予定の確認連絡をしました。',
       'cancel' => '授業予定のキャンセル連絡をしました。',
       'rest' => '休み連絡をしました。',
       'presence' => '授業を出席に更新しました。',
       'absence' => '授業を欠席に更新しました。',
     ];

     if($this->is_success_response($res)){
       $slack_type = 'info';
       $slack_message = $status_update_message[$status];
       switch($status){
         case "rest":
           //お休み連絡メール通知
           $this->rest_mail($param);
           break;
         case "confirm":
           //授業追加確認メール通知
           $this->confirm_mail($param);
           break;
         case "fix":
           //授業追加確定メール通知
           $this->fix_mail($param);
           break;
       }
     }
     $this->send_slack('カレンダーステータス更新['.$status.']:'.$slack_message.' / id['.$item['id'].']開始日時['.$item['start_time'].']終了日時['.$item['end_time'].']生徒['.$item['student_name'].']講師['.$item['teacher_name'].']', 'info', 'カレンダーステータス更新');
     return $this->save_redirect($res, $param, $status_update_message[$status]);
   }
   /**
    * カレンダーステータス更新
    *
    * @param  array  $param
    * @param  string  $status
    * @return \Illuminate\Http\Response
    */
   private function _status_update($param, $status){
     $item = $param['item'];
     try {
       DB::beginTransaction();
       //カレンダーステータス変更
       UserCalendarMember::where('calendar_id', $item->id)
           ->where('user_id', $param['user']->user_id)
           ->update(['status'=>$status]);
       UserCalendar::where('id', $item->id)->update(['status'=>$status]);

       //カレンダーメンバーステータス変更
       DB::commit();
       return $this->api_response(200, '', '', $item);
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
}
