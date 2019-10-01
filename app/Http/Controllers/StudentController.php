<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\UserCalendar;
use App\Models\StudentRelation;
use App\Models\GeneralAttribute;
use App\Models\Ask;
use App\Models\Tuition;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
class StudentController extends UserController
{
  public $domain = "students";
  public $table = "students";
  /**
   * このdomainで管理するmodel
   *
   * @return model
   */
  public function model(){
    return Student::query();
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
   $_table = $this->search($request);
   if($request->has('api')){
     $items = $_table['items'];
     foreach($items as $key=>$item){
       $items[$key] = $item->details();
     }
     return $this->api_response(200, '', '', $items);
   }
   return view($this->domain.'.tiles', $_table)
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
    $id = intval($id);
    $user = $this->login_details($request);
    if(empty($user)){
      //ログインしていない
      abort(419);
    }
    $ret = [
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'user' => $user,
       'mode'=>$request->mode,
       'search_word'=>$request->get('search_word'),
       '_status' => $request->get('status'),
       '_page' => $request->get('_page'),
       '_line' => $request->get('_line'),
       'list' => $request->get('list'),
       'attributes' => $this->attributes(),
    ];
    $ret['filter'] = [
      'search_week'=>$request->search_week,
      'search_work' => $request->search_work,
      'search_place' => $request->search_place,
    ];
    if(empty($ret['_line'])) $ret['_line'] = $this->pagenation_line;
    if(empty($ret['_page'])) $ret['_page'] = 0;
    if(is_numeric($id) && $id > 0){
      $ret['item'] = $this->model()->where('id', $id)->first();
      if(!isset($ret['item'])) abort(404);
      $ret['item'] = $ret['item']->user->details($this->domain);
      if($this->is_manager_or_teacher($user->role)==false){
        //講師、事務以外のアクセス制御
        if($this->is_student($user->role)){
          //生徒の場合自身のみアクセス可能
          if($ret['item']->id != $user->id) abort(403);
        }
        else {
          //保護者の場合、自分の子供のみアクセス可能
          if($ret['item']->is_parent($user->id)==false) abort(403);
        }
        if($ret['item']->status == 'unsubscribe'){
          //退会済み
          abort(403);
        }
      }
      $lists = ['cancel', 'confirm', 'exchange', 'month'];
      foreach($lists as $list){
        $calendars = $this->get_schedule(["list" => $list], $ret['item']->user_id);
        $ret[$list.'_count'] = $calendars["count"];
      }
      $asks = $this->get_ask([], $ret['item']->user_id);
      $ret['ask_count'] = $asks["count"];
    }
    else {
      if(!$this->is_manager_or_teacher($user->role)){
        abort(403);
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
  public function search(Request $request)
  {
    $items = $this->model()->with('user.image');

    $user = $this->login_details($request);
    if($this->domain==="students" && $this->is_parent($user->role)){
      //自分の子供のみ閲覧可能
      $items = $items->findChild($user->id);
    }
    else if($this->domain==="students" && $this->is_teacher($user->role)){
      //自分の担当生徒のみ閲覧可能
      $items = $items->findChargeStudent($user->id);
    }

    $items = $this->_search_scope($request, $items);

   $items = $this->_search_pagenation($request, $items);

   $items = $this->_search_sort($request, $items);

   $items = $items->get();
   return ["items" => $items];
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
      $items = $items->where($this->table.'.id',$request->id);
    }

    //検索ワード
    if(isset($request->search_word)){
      $items = $items->searchWord($request->search_word);
    }
    //ステータス
    if(isset($request->status)){
      if($request->status!='all'){
        $items = $items->findStatuses(explode(',', $request->status.','));
      }
    }
    else {
      $items = $items->findStatuses(['regular']);
    }
    return $items;
  }
  /**
   * 新規登録画面
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $param = $this->get_param($request);
    if(!$this->is_parent($param['user']->role)){
      abort(403);
    }
    $param['student'] = null;

    return view($this->domain.'.create',
      ['error_message' => ''])
      ->with($param);
   }

   /**
    * 新規登録
    *
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
     /*
     TODO: 申し込みベースで生徒を追加するので、不要
     $param = $this->get_param($request);
     if(!$this->is_parent($param['user']->role)){
       abort(403);
     }
     $form = $request->all();
     $parent = StudentParent::where('id', $param['user']->id)->first();
     $form['create_user_id'] = $param['user']->user_id;
     $student = $parent->brother_add($form);
     if(isset($student)){
       $form['parent_name_first'] = $param['user']->name_first;
       $form['parent_name_last'] = $param['user']->name_last;
       $form['send_to'] = 'parent';
       $this->send_mail($param['user']->email, '生徒情報登録完了', $form, 'text', 'register');
       $param['success_message'] = '生徒情報登録完了しました。';
     }
     else {
       $param['error_message'] = '生徒登録に失敗しました。';
     }
     return redirect('/home')
      ->with($param);
      */
   }
   /**
    * データ更新時のパラメータチェック
    *
    * @return \Illuminate\Http\Response
    */
   public function save_validate(Request $request)
   {
     //保存時にパラメータをチェック
     return $this->api_response(200, '', '');
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
   $model = $this->model()->where('id',$id);
   if(!isset($model)){
      abort(404);
   }
   if($request->has('api')){
     if(isset($model)) $model = $model->first()->details();
     return $this->api_response(200, '','', $model);
   }

   $model = $model->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   //コメントデータ取得
   $comments = $model->target_comments;
   if($this->is_teacher($user->role)){
     //講師の場合、公開されたコメントのみ閲覧可能
     $comments = $comments->where('publiced_at', '<=' , Date('Y-m-d'));
   }
   $comments = $comments->sortByDesc('created_at');

   //目標データ取得
   $milestones = $model->target_milestones;
   $view = "page";

   $param['view'] = $view;
   return view($this->domain.'.'.$view, [
     'item' => $item,
     'comments'=>$comments,
     'milestones'=>$milestones,
   ])->with($param);
  }
  public function unsubscribe(Request $request, $id)
  {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id);
   if(!isset($model)){
      abort(404);
   }
   $model = $model->first()->user;
   $item = $model->details();
   $user = $param['user'];
   //重複登録があるかチェック
   $param['recess_data'] = $item->already_recess_data($param['user']->user_id);
   $param['already_data'] = $item->already_unsubscribe_data($param['user']->user_id);
   return view('asks.unsubscribe', [
   ])->with($param);
  }
  public function recess(Request $request, $id)
  {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id);
   if(!isset($model)){
      abort(404);
   }
   $model = $model->first()->user;
   $item = $model->details();
   $user = $param['user'];

   //重複登録があるかチェック
   $param['already_data'] = $item->already_recess_data($param['user']->user_id);
   $param['unsubscribe_data'] = $item->already_unsubscribe_data($param['user']->user_id);
   return view('asks.recess', [
   ])->with($param);
  }
  /**
   * 詳細画面表示
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
 public function calendar(Request $request, $id)
 {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   //目標データ取得
   $milestones = $model->target_milestones;

   $view = "calendar";
   $param['view'] = $view;
   return view($this->domain.'.'.$view, [
     'item' => $item,
     'milestones'=>$milestones,
   ])->with($param);
 }
 public function schedule(Request $request, $id)
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

   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];
   //status: new > confirm > fix >rest, presence , absence
   //other status : cancel


   $view = "schedule";
   $calendars = $this->get_schedule($request->all(), $item->user_id);
   $page_data = $this->get_pagedata($calendars["count"] , $param['_line'], $param["_page"]);
   foreach($page_data as $key => $val){
     $param[$key] = $val;
   }
   $calendars = $calendars["data"];
   foreach($calendars as $calendar){
     $calendar = $calendar->details();
   }
   $param["calendars"] = $calendars;

   $filter = [];
   $filter_keys = ['search_from_date', 'search_to_date', 'is_exchange', 'search_status', 'search_work', 'search_place', 'is_desc', ''];
   foreach($filter_keys as $filter_key){
     if($request->has($filter_key)){
       $filter[$filter_key] = $request->get($filter_key);
     }
   }
   $param['filter'] = $filter;
   $param['view'] = $view;
   return view($this->domain.'.'.$view, [
     'item' => $item,
   ])->with($param);
 }

 public function ask(Request $request, $id)
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

   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   $view = "ask";
   $asks = $this->get_ask($request->all(), $item->user_id);
   $page_data = $this->get_pagedata($asks["count"] , $param['_line'], $param["_page"]);
   foreach($page_data as $key => $val){
     $param[$key] = $val;
   }
   $asks = $asks["data"];
   foreach($asks as $ask){
     $ask = $ask->details();
   }
   $param["asks"] = $asks;

   $filter = [];
   $filter_keys = ['search_type', 'search_status'];
   foreach($filter_keys as $filter_key){
     if($request->has($filter_key)){
       $filter[$filter_key] = $request->get($filter_key);
     }
   }
   $param['filter'] = $filter;
   $param['view'] = $view;
   return view($this->domain.'.'.$view, [
     'item' => $item,
   ])->with($param);
 }

 public function tuition(Request $request, $id)
 {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();
   $user = $param['user'];

   $view = "tuition";
   $tuitions = $this->get_tuition($request->all(), $item->id);
   $tuitions = $tuitions["data"];
   foreach($tuitions as $tuition){
     $tuition = $tuition->details();
   }
   $param["tuitions"] = $tuitions;

   $filter = [];
   $filter_keys = ['search_lesson'];
   foreach($filter_keys as $filter_key){
     if($request->has($filter_key)){
       $filter[$filter_key] = $request->get($filter_key);
     }
   }
   $param['filter'] = $filter;
   $param['view'] = $view;
   return view($this->domain.'.'.$view, [
     'item' => $item,
   ])->with($param);
 }
 public function calendar_settings(Request $request, $id)
 {
   $param = $this->get_param($request, $id);
   $model = $this->model()->where('id',$id)->first()->user;
   $item = $model->details();
   $item['tags'] = $model->tags();

   $user = $param['user'];
   $view = "calendar_settings";
   $param['view'] = $view;
   $calendar_settings = $item->get_calendar_settings($param['filter']);
   return view($this->domain.'.'.$view, [
     'calendar_settings' => $calendar_settings,
   ])->with($param);
 }
 public function get_schedule($form, $user_id, $from_date = '', $to_date = ''){
   $user = User::where('id', $user_id)->first()->details();
   $form['_sort'] ='start_time';
   if(!isset($form['list'])) $form['list'] = '';
   switch($form['list']){
     case "history":
       break;
     case "exchange":
       $form['is_exchange'] = 1;
       break;
     case "cancel":
       if(!isset($form['search_from_date'])){
         $form['search_from_date'] = date('Y-m-d', strtotime("now"));
       }
       if(!isset($form['search_to_date'])){
         $form['search_to_date'] = date('Y-m-d', strtotime("+14 day"));
       }
       if(!isset($form['search_status'])){
         $form['search_status'] = ['cancel', 'rest', 'lecture_cancel'];
       }
       break;
     case "confirm":
       if(!isset($form['search_status'])){
         if($this->is_student_or_parent($user->role)){
           $form['search_status'] = ['confirm'];
         }
         else {
           $form['search_status'] = ['new', 'confirm'];
         }
       }
       break;
     case "today":
       if(!isset($form['search_from_date'])){
         $form['search_from_date'] = date('Y-m-d', strtotime("now"));
       }
       if(!isset($form['search_to_date'])){
         $form['search_to_date'] = date('Y-m-d', strtotime("+1 day"));
       }
       if(!isset($form['search_status'])){
         $form['search_status'] = ['rest', 'fix', 'presence', 'absence', 'lecture_cancel'];
       }
       break;
     case "month":
       //当月指定
       if(!isset($form['search_from_date'])){
         $form['search_from_date'] = date('Y-m-1', strtotime("now"));
       }
       if(!isset($form['search_to_date'])){
         $form['search_to_date'] = date('Y-m-t', strtotime("now"));
       }
       if(!isset($form['search_status'])){
         $form['search_status'] = ['rest', 'fix', 'presence', 'absence', 'lecture_cancel'];
       }
       break;
     default:
       if(!isset($form['search_status'])){
         $form['search_status'] = ['rest', 'fix', 'presence', 'absence', 'lecture_cancel'];
       }
       break;
   }
   $statuses = [];
   $is_exchange = false;
   $is_desc = false;
   if(empty($from_date)) $from_date = date('Y-m-1', strtotime("-1 month"));
   if(empty($to_date)) $to_date = date('Y-m-t', strtotime("+1 month"));

   if(isset($form['is_exchange']) && $form['is_exchange']==1){
     $is_exchange = true;
   }
   $sort = 'asc';
   if(isset($form['is_desc']) && $form['is_desc']==1){
     $sort = 'desc';
   }
   if(isset($form['search_from_date'])){
     $from_date = $form['search_from_date'];
   }
   if(isset($form['search_to_date'])){
     $to_date = $form['search_to_date'];
   }
   if(isset($form['search_status'])){
     $statuses = $form['search_status'];
   }
   $works =[];
   if(isset($form['search_work'])){
     $works = $form['search_work'];
   }
   $places =[];
   if(isset($form['search_place'])){
     $places = $form['search_place'];
   }
   $calendars = UserCalendar::rangeDate($from_date, $to_date);
   $calendars = $calendars->findStatuses($statuses);
   $calendars = $calendars->findWorks($works);
   $calendars = $calendars->findPlaces($places);
   $calendars = $calendars->findUser($user_id);
   if($is_exchange==true){
     $calendars = $calendars->findExchangeTarget();
   }
   $count = $calendars->count();
   $calendars = $calendars->sortStarttime($sort);

   if(isset($form['_page']) && isset($form['_line'])){
     $calendars = $calendars->pagenation(intval($form['_page'])-1, $form['_line']);
   }
   //echo $calendars->toSql()."<br>";
   $calendars = $calendars->get();
   if($this->domain=='students'){
     foreach($calendars as $i=>$calendar){
       $calendars[$i]->own_member = $calendars[$i]->get_member($user_id);
       $calendars[$i]->status = $calendars[$i]->own_member->status;
     }
   }


   return ["data" => $calendars, "count" => $count];
 }
 public function get_ask($form, $user_id){
   if(!isset($form['list'])) $form['list'] = '';
   $default_sttus = 'new';
   switch($form['list']){
     case "teacher_change":
       if(!isset($form['search_type'])){
         $form['search_type'] = ['teacher_change'];
       }
       break;
     case "lecture_cancel":
       if(!isset($form['search_type'])){
         $form['search_type'] = ['lecture_cancel'];
       }
       break;
     case "unsubscribe":
       if(!isset($form['search_type'])){
         $form['search_type'] = ['unsubscribe'];
         $default_sttus = 'commit';
       }
       break;
     case "recess":
       if(!isset($form['search_type'])){
         $form['search_type'] = ['recess'];
         $default_sttus = 'commit';
       }
       break;
   }

   if(!isset($form['search_type'])){
     $form['search_type'] = [];
   }
   if(!isset($form['search_status'])){
     $form['search_status'] = [$default_sttus];
   }
   $form['_sort'] ='end_date';

   $statuses = [];
   $types = [];
   $is_desc = false;

   $sort = 'asc';
   if(isset($form['is_desc']) && $form['is_desc']==1){
     $sort = 'desc';
   }
   if(isset($form['search_status'])){
     $statuses = $form['search_status'];
   }
   if(isset($form['search_type'])){
     $types = $form['search_type'];
   }
   $asks = Ask::findStatuses($statuses);
   $asks = $asks->findTypes($types);
   $asks = $asks->findStatuses($statuses);
   $u = User::where('id', $user_id)->first()->details('managers');
   if(!$this->is_manager($u->role)) $asks = $asks->findUser($user_id);
   $count = $asks->count();
   $asks = $asks->sortEnddate($sort);

   if(isset($form['_page']) && isset($form['_line'])){
     $asks = $asks->pagenation(intval($form['_page'])-1, $form['_line']);
   }
   //echo $asks->toSql();
   $asks = $asks->get();
   return ["data" => $asks, "count" => $count];
 }

 public function get_tuition($form, $id){
   if(!isset($form['list'])) $form['list'] = '';
   $default_sttus = 'new';
   switch($form['list']){
   }

   if(!isset($form['search_lesson'])){
     $form['search_lesson'] = [];
   }

   $form['_sort'] ='lesson, course_type, kids_lesson';

   $statuses = [];
   $types = [];
   $is_desc = false;

   $sort = 'asc';
   if(isset($form['is_desc']) && $form['is_desc']==1){
     $sort = 'desc';
   }

   $tuitions = Tuition::where('tuition','>', '0');
   if($this->domain == "students"){
     $tuitions->where('student_id', $id);
   }
   else if($this->domain == "teachers"){
     $tuitions->where('teacher_id', $id);
   }

   $count = $tuitions->count();

   //echo $tuitions->toSql();
   $tuitions = $tuitions->get();
   return ["data" => $tuitions, "count" => $count];
 }

 /**
  * Show the form for agreement the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
 public function agreement_page(Request $request, $id)
 {
   $result = '';
   $param = $this->get_param($request, $id);
   $param['fields'] = [];
   $param['_edit'] = true;
   $param['student'] = $param['item'];
   return view($this->domain.'.agreement',$param);

 }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $result = '';
    $param = $this->get_param($request, $id);
    $param['_edit'] = true;
    $param['student'] = $param['item'];
    return view($this->domain.'.edit',$param);

  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function tag_page(Request $request, $id)
  {
    $result = '';
    $param = $this->get_param($request, $id);
    $param['_edit'] = true;
    $param['student'] = $param['item'];
    return view($this->domain.'.tag',$param);

  }
  public function delete_page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $param['item']['name'] = $param['item']->name();
    $param['item']['kana'] = $param['item']->kana();
    $param['item']['birth_day'] = $param['item']->birth_day();
    $param['item']['gender'] = $param['item']->gender();
    $fields = [
      'name' => [
        'label' => __('labels.name'),
      ],
      'kana' => [
        'label' => 'フリガナ',
      ],
      'birth_day' => [
        'label' => '生年月日',
      ],
      'gender' => [
        'label' => '性別',
      ],
    ];
    return view('components.page', [
      'action' => 'delete',
      'fields'=>$fields])
      ->with($param);
  }
  public function remind_page(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $param['item']['name'] = $param['item']->name();
    $fields = [
      'id' => [
        'label' => 'ID',
      ],
      'name' => [
        'label' => __('labels.name'),
      ],
    ];
    return view('components.page', [
      'action' => 'remind',
      'fields'=>$fields])
      ->with($param);
  }

  public function remind(Request $request, $id)
  {
    $result = '';
    $form = $request->all();
    $res = $this->api_response(200);
    $access_key = $this->create_token(1728000);    //token期限＝20日
    $param = $this->get_param($request, $id);
    $result = '';
    $email = $param['item']['email'];
    $status = intval($param['item']->user->status);
    $message = '本登録依頼メールを送信しました。';
    if(isset($form['email'])){
      //入力値としてemailがある場合はそちらを優先する
      $email = $form['email'];
      $already_user = User::where(['email' => $email])->first();
      if(!isset($already_user)){
        //既存のユーザーに同じメールアドレスが存在しない
        $param['item']->user->update(['email' => $email]);
      }
    }
    if($status==1){
      //token更新
      $param['item']->user->update( ['access_key' => $access_key]);
      $result = 'success';
    }
    $send_to = $param['item']->role;
    if($send_to=='staff') $send_to = 'manager';

    if($this->is_success_response($res)){
      $title = "システム本登録のお願い";
      $this->send_mail($email,
        $title, [
        'user_name' => $param['item']['name'],
        'access_key' => $access_key,
        'remind' => true,
        'send_to' => $send_to,
      ], 'text', 'entry');
    }
    return $this->save_redirect($res, $param, $message);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    $res = $this->_update($request, $id);
    return $this->save_redirect($res, $param, '設定を更新しました。');
  }

  public function _update(Request $request, $id)
  {
    $res = $this->save_validate($request);
    if(!$this->is_success_response($res)){
      return $res;
    }
    return $this->transaction(function() use ($request, $id){
       $user = $this->login_details($request);
       $form = $request->all();
       $form['create_user_id'] = $user->user_id;
       $item = $this->model()->where('id',$id)->first();
       $item = $item->profile_update($form);
       return $item;
    }, '生徒情報更新', __FILE__, __FUNCTION__, __LINE__ );
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $param = $this->get_param($request, $id);

    $res = $this->_delete($request, $id);
    return $this->save_redirect($res, $param, '削除しました。');
  }

  public function _delete(Request $request, $id)
  {
   $form = $request->all();
   try {
     DB::beginTransaction();
     $item = $this->model()->where('id', $id)->first()->user->update(['status' => 9]);
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
