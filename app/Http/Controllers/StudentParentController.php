<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentRelation;
use App\Models\Trial;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

class StudentParentController extends TeacherController
{
  public $domain = "parents";
  public $table = "student_parents";
  public $domain_name = "ご契約者様";
  public function model(){
    return StudentParent::query();
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
    $user = $this->login_details();
    $pagenation = '';
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'mode'=>$request->mode,
      'search_word'=>$request->get('search_word'),
      '_status' => $request->get('status'),
      '_page' => $request->get('_page'),
      '_line' => $request->get('_line'),
      'list' => $request->get('list'),
      'attributes' => $this->attributes(),
    ];
    if(empty($ret['_line'])) $ret['_line'] = $this->pagenation_line;
    if(empty($ret['_page'])) $ret['_page'] = 0;
    if(empty($user)){
      //ログインしていない
      abort(419);
    }
    if(is_numeric($id) && $id > 0){
      //id指定がある
      if(!($this->is_manager($user->role) || $this->is_manager($user->role)) && $id!==$user->id){
        //講師事務以外は自分のidしか閲覧できない
        abort(403);
      }
      $ret['item'] = $this->model()->where('id',$id)->first()->user->details($this->domain);
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
    $user = $this->login_details();
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


    $charge_students = $this->get_students($request, $id);

    return view($this->domain.'.page', [
      'charge_students'=>$charge_students,
    ])->with($param);
  }
  private function get_students(Request $request, $parent_id){
    $students =  StudentRelation::with('student')->findParent($parent_id)
      ->likeStudentName($request->search_word)
      ->get();
    foreach($students as $student){
      $student['current_calendar_start_time'] = $student->current_calendar()['start_time'];
      if(empty($student['current_calendar_start_time'])){
        //予定があるものを上にあげて、昇順、予定がないもの（null)を後ろにする
        $student['current_calendar_start_time'] = '9999-12-31 23:59:59';
      }
    }
    $students = $students->sortBy('current_calendar_start_time');
    return $students;
  }

  /**
   * 体験授業申し込みページ
   *
   * @return \Illuminate\Http\Response
   */
  public function entry(Request $request)
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
   public function entry_store(Request $request)
   {
     $result = '';
     $form = $request->all();
     $res = $this->api_response(200);
     $access_key = $this->create_token();
     $request->merge([
       'access_key' => $access_key,
     ]);
     $user = User::where('email', $form['email'])->first();
     $result = '';
     if(!isset($user)){
       $res = $this->_entry_store($request);
       $result = 'success';
     }
     else {
       if($user->status===1){
         //すでにユーザーが仮登録されている場合は、tokenを更新
         $user->update( ['access_key' => $access_key]);
         $result = 'already';
       }
       else {
         //本登録済み
         $res = $this->error_response('このメールアドレスは本登録が完了しております。');
       }
     }
     if($this->is_success_response($res)){
       $this->send_mail($form['email'],
         'お申込み仮受付完了', [
         'user_name' => $form['name_last'].' '.$form['name_first'],
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
   public function _entry_store(Request $request)
   {
     $form = $request->all();
     try {
       DB::beginTransaction();
       $form["password"] = 'sakusaku';
       $items = StudentParent::entry($form);
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
    * 本登録ページ
    *
    * @return \Illuminate\Http\Response
    */
   public function register(Request $request)
   {
     $result = '';
     $param = [
       'domain' => $this->domain,
       'domain_name' => $this->domain_name,
       'user' => $this->login_details(),
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
       $user = User::where('access_key',$access_key);
       if($user->count() < 1){
         abort(404);
       }
       $param['parent'] = $user->first()->details();
       $student = Student::findChild($param['parent']->id)->orderBy('id', 'desc')->first();
       $param['trial'] = Trial::where('student_id', $student->id)->where('student_parent_id', $param['parent']->id)->first()->details();
       $param['student'] = $student;
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
        'user' => $this->login_details(),
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
      $res = $this->_register_update($request);
      $email = $form['email'];
      $password = $form['password'];
      if($this->is_success_response($res)){
        if(empty($param['user'])){
          $form['send_to'] = 'parent';
          $this->send_mail($email, 'ご入会お申込みありがとうございます', $form, 'text', 'register');
          if (!Auth::attempt(['email' => $email, 'password' => $password]))
          {
            abort(500);
          }
        }
        return $this->save_redirect($res, $param, 'ご入会お申込みありがとうございます', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key']);
      if($user->count() < 1){
        abort(403);
      }
      try {
        $user = $user->first();
        DB::beginTransaction();
        $form['create_user_id'] = $user->id;

        $parent = StudentParent::where('id', $form['parent_id'])->first();
        $parent->profile_update([
          'name_last' => $form['parent_name_last'],
          'name_first' => $form['parent_name_first'],
          'kana_last' => $form['parent_kana_last'],
          'kana_first' => $form['parent_kana_first'],
          'phone_no' => $form['phone_no'],
          'create_user_id' => $form['create_user_id'],
        ]);

        $student = Student::where('id', $form['student_id'])->first();
        $student->profile_update([
          'name_last' => $student->name_last,
          'name_first' => $student->name_first,
          'gender' => $student->gender,
          'kana_last' => $form['student_kana_last'],
          'kana_first' => $form['student_kana_first'],
          'phone_no' => $form['phone_no'],
          'create_user_id' => $form['create_user_id'],
        ]);
        $student->user->update(['status' => 0]);
        $user->set_password($form['password']);
        $user->update(['status' => 0]);
        if(!empty($form['remark'])){
          Comment::create([
            'title' => '入会時・ご要望',
            'body' => $form['remark'],
            'type' => 'study',
            'status' => 'new',
            'publiced_at' => date('Y-m-d'),
            'target_user_id' => $student->user_id,
            'create_user_id' => $form['create_user_id'],
          ]);
        }
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

}
