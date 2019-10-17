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
    $user = $this->login_details($request);
    $pagenation = '';
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
    if(empty($ret['_line'])) $ret['_line'] = $this->pagenation_line;
    if(empty($ret['_page'])) $ret['_page'] = 0;
    if(!isset($user)){
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


    $charge_students = $this->get_students($request, $id);

    return view($this->domain.'.page', [
      'charge_students'=>$charge_students,
    ])->with($param);
  }
  private function get_students(Request $request, $parent_id){
    $students =  StudentRelation::with('student')->findParent($parent_id)
      ->get();
    foreach($students as $key => $student){
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
      'domain_name' => __('labels.'.$this->domain),
      'attributes' => $this->attributes(),
    ];
    return view($this->domain.'.entry',
      ['sended' => ''])
      ->with($param);
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
       $user = User::where('access_key',$access_key);
       if($user->count() < 1){
         abort(404);
       }
       $param['parent'] = $user->first()->details();
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
      $res = $this->_register_update($request);
      $email = $form['email'];
      $password = $form['password'];
      if($this->is_success_response($res)){
        if(empty($param['user'])){
          $form['send_to'] = 'parent';
          $this->send_mail($email, 'システムへの本登録が完了しました', $form, 'text', 'register');
          if (!Auth::attempt(['email' => $email, 'password' => $password]))
          {
            abort(400, 'ログインできない['.$email.']['.$password.']['.$access_key.']');
          }
        }
        return $this->save_redirect($res, $param, 'システムへの本登録が完了しました', '/home');
      }
      return $this->save_redirect($res, $param, '', $this->domain.'/register');
    }
    public function _register_update($form)
    {
      $user = User::where('access_key',$form['access_key']);
      if($user->count() < 1){
        abort(403);
      }
      $user = $user->first();
      return $this->transaction($request, function() use ($form, $user){
        $form['create_user_id'] = $user->id;
        $parent = StudentParent::where('user_id', $user->id)->first();
        $parent->profile_update([
          'name_last' => $form['parent_name_last'],
          'name_first' => $form['parent_name_first'],
          'kana_last' => $form['parent_kana_last'],
          'kana_first' => $form['parent_kana_first'],
          'phone_no' => $form['phone_no'],
          'status' => 'regular',
          'create_user_id' => $form['create_user_id'],
        ]);

        $user->set_password($form['password']);
        $user->update(['status' => 0]);
/*
        $trial = Trial::where('id', $form['trial_id'])->first();
        //体験申し込みの生徒を本登録
        foreach($trial->trial_students as $trial_student){
          $trial_student->student->regular();
        }
*/
        return $user;
      }, '契約者登録', __FILE__, __FUNCTION__, __LINE__ );
    }

}
