<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentRelation;
use App\Models\Trial;
use App\Models\Comment;
use App\Models\Ask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

class StudentParentController extends TeacherController
{
  public $domain = "parents";
  public $table = "student_parents";

  public function model(){
    return StudentParent::query();
  }
  public function empty_model(){
    return new StudentParent;
  }

  /**
   * 共通パラメータ取得
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id　（this.domain.model.id)
   * @return json
   */
  public function get_param(Request $request, $id=null){
    $ret = $this->get_common_param($request);
    $user = $ret['user'];
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if(!($this->is_manager($user->role) || $this->is_manager($user->role)) && $id!=$user->id){
        //講師事務以外は自分のidしか閲覧できない
        abort(403, $id."!==".$user->id);
      }
      //$ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
      $ret['item'] = $this->model()->where('id',$id)->first();
      if(!isset($ret['item'])) abort(404);
      $ret['item'] = $ret['item']->details();
      $ret['charge_students'] = $this->get_students($request, $id);
    }
    else {
      //id指定がない、かつ、事務以外はNG
      if($this->is_manager($user->role)!==true){
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
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show(Request $request, $id)
  {
    $param = $this->get_param($request, $id);
    /*
    $model = $this->model()->where('id',$id)->first()->user;
    $item = $model->details();
    $item['tags'] = $model->tags();
    */
    $user = $param['user'];
    $param['view'] = "page";
    return view($this->domain.'.page', [
    ])->with($param);
  }
  private function get_students(Request $request, $parent_id){
    $students =  StudentRelation::with('student')->findParent($parent_id)
      ->get();
    foreach($students as $key => $student){
      $current_calendar = $student->current_calendar();
      if(isset($current_calendar)) $student['current_calendar_start_time'] = $current_calendar['start_time'];
      if(empty($student['current_calendar_start_time'])){
        //予定があるものを上にあげて、昇順、予定がないもの（null)を後ろにする
        $student['current_calendar_start_time'] = '9999-12-31 23:59:59';
      }
    }
    $students = $students->sortBy('current_calendar_start_time');
    return $students;
  }

  /**
   * ユーザー登録
   *　TODO　体験授業申し込みページ側で、登録されるようにしていたが、
   * BtoCでの利用を考えると、体験授業なしで、登録できる仕組みがあったほうがよい
   * @return \Illuminate\Http\Response
   */
  public function entry(Request $request)
  {
    $param = [
      'domain' => $this->domain,
      'domain_name' => __('labels.'.$this->domain),
      'attributes' => $this->attributes(),
    ];
    return view($this->domain.'.entry',
      ['result' => ''])
      ->with($param);
   }
   /**
    * 仮登録処理
    *
    * @return \Illuminate\Http\Response
    */
   public function entry_store(Request $request)
   {
     $param = [
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'attributes' => $this->attributes(),
       'email' => $request->get('email'),
     ];

     $result = '';
     $form = $request->all();
     $res = $this->api_response(200);
     $access_key = $this->create_token();

     $user = User::where('email', $form['email'])->first();
     $result = '';
     if(!isset($user)){
       $res =  $this->transaction($request, function() use ($request,$access_key){
         $form = $request->all();
         $form["access_key"] = $access_key;
         $form["name_last"] = '';
         $form["name_first"] = '';
         $form["phone_no"] = '';
         $form["address"] = '';
         $form["post_no"] = '';
         $form["password"] = 'sakusaku';
         $item = StudentParent::entry($form);
         return $this->api_response(200, '', '', $item);
       }, __('labels.'.$this->domain).'登録', __FILE__, __FUNCTION__, __LINE__ );
       $result = 'success';
     }
     else {
       if($user->status===1){
         //すでにユーザーが仮登録されている場合は、tokenを更新
         $user->update( ['access_key' => $access_key]);
         $result = 'success';
       }
       else {
         //本登録済み
         $result = 'already';
       }
     }
     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         '仮登録完了', [
         'access_key' => $access_key,
       ], 'text', 'signup');
     }
     else {
       $result = $res['message'];
       $param['res'] = $res;
     }
     return view($this->domain.'.entry',
       ['result' => $result])->with($param);
   }

   /**
    * 本登録ページ
    *
    * @return \Illuminate\Http\Response
    */
   public function register(Request $request)
   {
     $result = '';
     $param = [
       'domain' => $this->domain,
       'domain_name' => __('labels.'.$this->domain),
       'user' => $this->login_details($request),
       'attributes' => $this->attributes(),
     ];
     if(!empty($param['user'])){
       $param['result'] = 'logout';
       return view($this->domain.'.register',$param);
     }
     else {
       $access_key = $request->get('key');
       if(!$this->is_enable_token($access_key)){
         $param['result'] = 'token_error';
         return view($this->domain.'.register',$param);
       }
       $user = User::where('access_key',$access_key)->first();
       if(!isset($user)){
         abort(404);
       }
       $param['parent'] = $user->details();
       $param['user'] = $param['parent'];
       $student = Student::findChild($param['parent']->id)->orderBy('id', 'desc')->first();
       $trial = Trial::where('student_parent_id', $param['parent']->id)->where('student_parent_id', $param['parent']->id)->first();
       if(isset($trial)) $param['trial'] = $trial->details();
       $param['access_key'] = $access_key;
       $param['_edit'] = false;
     }
     return view($this->domain.'.register',$param);
    }
    /**
     * 本登録処理
     *
     * @return \Illuminate\Http\Response
     */
    public function register_update(Request $request)
    {
      $param = [
        'user' => $this->login_details($request),
        'attributes' => $this->attributes(),
      ];
      $result = "success";
      $email = "";
      $password = "";
      $form = $request->all();
      if(!empty($param['user'])){
        //ログインユーザーがある場合は、操作させない
        //一度ログアウト
        Auth::logout();
      }
      $access_key = $request->access_key;
      if(!$this->is_enable_token($access_key)){
        $result = "token_error";
        return view($this->domain.'.register',
          ['result' => $result]
        );
      }
      $user = User::where('access_key',$form['access_key'])->first();
      if(!isset($user)){
        abort(403);
      }

      $res = $this->_register_update($request);
      if($this->is_success_response($res)){
        if(empty($param['user'])){
          $form['send_to'] = 'parent';
          $this->send_mail($user->email, 'システムへの本登録が完了しました', $form, 'text', 'register');
          Auth::loginUsingId($user->id);
        }
        return $this->save_redirect($res, $param, 'システムへの本登録が完了しました', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update(Request $request)
    {
      $form = $request->all();

      $user = User::where('access_key',$form['access_key'])->first();
      if(!isset($user)){
        abort(403);
      }
      return $this->transaction($request, function() use ($form, $user){
        $form['create_user_id'] = $user->id;
        $parent = StudentParent::where('user_id', $user->id)->first();
        if(!isset($parent)){
          $parent = StudentParent::create([
                  'name_last' => $form['parent_name_last'],
                  'name_first' => $form['parent_name_first'],
                  'kana_last' => $form['parent_kana_last'],
                  'kana_first' => $form['parent_kana_first'],
                  'phone_no' => $form['phone_no'],
                  'post_no' => $form['post_no'],
                  'address' => $form['address'],
                  'status' => 'regular',
                  'user_id' => $user->id,
                  'create_user_id' => $user->id,
                ]);
        }
        else {
          $parent->profile_update([
            'name_last' => $form['parent_name_last'],
            'name_first' => $form['parent_name_first'],
            'kana_last' => $form['parent_kana_last'],
            'kana_first' => $form['parent_kana_first'],
            'phone_no' => $form['phone_no'],
            'post_no' => $form['post_no'],
            'address' => $form['address'],
            'status' => 'regular',
          ]);
        }
        $user->set_password($form['password']);
        $user->update(['status' => 0]);
        return $this->api_response(200, "", "", $user);
      }, 'アカウント登録', __FILE__, __FUNCTION__, __LINE__ );
    }
    public function trial_request_page(Request $request, $id)
    {
      if(!$request->has('student_id')) abort(403);
      $student = Student::where('id', $request->get('student_id'))->first();
      if(!isset($student)) abort(403);
      $param = $this->get_param($request, $id);
      $edit = false;

      //登録申し込み情報
      $trial = $student->trial();
      if($trial!=null){
        $param['item'] = $trial;
        $edit = true;
      }
      else {
        $param['item'] = new Trial();
      }
      $param['student1'] = $student;
      $param['student_parent_id'] = $id;
      return view('trials.trial_request', [
        '_edit' => $edit])
        ->with($param);
    }
    public function trial_request(Request $request, $id)
    {

      $form = $request->all();
      if(!$request->has('student_id')) abort(403);
      $param = $this->get_param($request, $id);
      $access_key = $this->create_token();
      $request->merge([
        'access_key' => $access_key,
      ]);
      $form = $request->all();
      $form['student_parent_id'] = $id;
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
      $form['create_user_id'] = $param['user']->user_id;
      $res = $this->transaction($request, function() use ($form){
        $item = Trial::entry($form);
        return $this->api_response(200, '', '', $item);
      }, '体験授業申込', __FILE__, __FUNCTION__, __LINE__ );

      if($this->is_success_response($res)){
        $param['item']->user->send_mail(
          '体験授業のお申込み、ありがとうございます', [
          'user_name' => $form['student_name_last'].' '.$form['student_name_first'],
          'access_key' => $access_key,
          'send_to' => 'parent',
        ], 'text', 'trial');
      }
      return $this->save_redirect($res, [], '');
    }
}
