<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Lecture;
use App\Models\ChargeStudent;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use DB;
use View;
class UserCalendarController extends MilestoneController
{
  public $domain = 'calendars';
  public $table = 'user_calendars';
  public $domain_name = 'カレンダー';
  public function model(){
    return UserCalendar::query();
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
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'teacher_id' => $request->teacher_id,
      'manager_id' => $request->manager_id,
      'student_id' => $request->student_id,
      'search_word'=>$request->search_word,
      'search_status'=>$request->status
    ];
    if(is_numeric($id) && $id > 0){
      $item = $this->model()->where('id','=',$id)->first();
      $ret['item'] = $this->get_details($item);
    }

    return $ret;
  }
  /**
   * このユーザーのカレンダーにアクセス可能かどうか判定
   *
   * @param  int  $user_id
   * @return json
   */
  public function check_role($user_id){
    $user = $this->login_details();
    if(is_numeric($user_id) && $user_id > 0 && $user_id != $user->user_id){
      //他ユーザーのカレンダーデータを取得する判定
      if($this->is_student($user->role)===true){
        //生徒の場合はNG
        return $this->forbidden("is student");
      }
      $target_user = User::where('id', $user_id)->first();
      if(!isset($target_user)){
        //存在しない
        return $this->notfound();
      }
      $target_user = $target_user->details();

      if($this->is_parent($user->role)===true){
        if(!$this->is_student($target_user->role)===true){
          //保護者の場合は、生徒のカレンダーしか見れない
          return $this->forbidden("is not student");
        }
        $relation = StudentRelation::where('student_id', $target_user->id)->where('student_parent_id', $user->id)->first();
        if(!isset($relation)){
          //保護者の場合は自分の家族以外はNG
          return $this->forbidden("is not family");
        }
      }
      else if($this->is_teacher($user->role)===true){
        if(!$this->is_student($target_user->role)===true){
          //講師の場合は、生徒のカレンダーしか見れない
          return $this->forbidden("is not student");
        }
        $charge_student = ChargeStudent::where('teacher_id', $user->id)
          ->where('student_id', $target_user->id)->first();
          if(!isset($charge_student)){
            //講師の場合は、担当生徒以外は、NG
            return $this->forbidden("is not charge student");
          }
      }
    }
    else {
      //指定がなければ自分のカレンダー
      $user_id = $user->user_id;
    }

    return $this->api_response(200, "", "");
  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      /*
      $param = $this->get_param($request);
      */
      /*
      $items = $this->model()->first();
      $items = UserCalendar::all();
      */
      $items = DB::table($this->table)->get();
      return $items->toArray();
/*
      $_table = $this->search($request, 309);
      return $_table;
*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function api_index(Request $request, $user_id=0, $from_date=null, $to_date=null)
    {
      /*
      $param = $this->get_param($request);
      $res = $this->check_role($user_id);
      if(!$this->is_success_response($res)){
        return $res;
      }
      */

      if(!empty($from_date) && strlen($from_date)===8){
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        $request->merge([
          'from_date' => $from_date,
        ]);
      }
      if(!empty($to_date) && strlen($to_date)===8){
        $to_date = date('Y-m-d 00:00:00', strtotime($to_date));
        $request->merge([
          'to_date' => $to_date,
        ]);
      }
      $_table = $this->search($request, $user_id);

      return $this->api_response(200, "", "", $_table);
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
      $where_raw = <<<EOT
        $this->table.id in (select calendar_id from user_calendar_members where user_id=?)
EOT;

      $items = $items->whereRaw($where_raw,[$user_id]);

      $items = $this->_search_scope($request, $items);
      $items = $this->_search_sort($request, $items);
      $items = $items->get();
      foreach($items as $item){
        $item = $item->details();
      }

      return $items->toArray();
    }
    private function get_details($calendar){
      $item = $calendar;
      $item['status_name'] = $calendar->status_name();
      $item['place'] = $calendar->place();
      $item['date'] = date('Y/m/d',  strtotime($item['start_time']));
      $item['start'] = date('H:i',  strtotime($item['start_time']));
      $item['end'] = date('H:i',  strtotime($item['end_time']));

      $lecture = $calendar->lecture->details();
      $item['subject'] = $lecture['subject']->attribute_name;
      $item['lesson'] = $lecture['lesson']->attribute_name;
      $item['course'] = $lecture['course']->attribute_name;

      $teacher_name = "";
      $student_name = "";
      $other_name = "";
      foreach($item->members as $member){
        $_member = User::where('id', $member->user_id)->first()->details();
        if($_member->role === 'student'){
          $student_name.=$_member['name'].',';
        }
        else if($_member->role === 'teacher'){
          $teacher_name.=$_member['name'].',';
        }
        else {
          $other_name.=$_member['name'].',';
        }
      }
      unset($item['members']);
      unset($item['lecture']);
      $item['student_name'] = trim($student_name,',');
      $item['teacher_name'] = trim($teacher_name,',');
      $item['other_name'] = trim($other_name,',');
      return $item;
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
      if(isset($request->search_status)){
        $items = $items->where('status',$request->search_status);
      }
      //更新取得
      if(isset($request->update)){
        $items = $items->where('updated_at','>',$request->update);
      }
      //日付検索
      if(isset($request->from_date)){
        $items = $items->where('start_time', '>=', $request->from_date);
      }
      if(isset($request->to_date)){
        $items = $items->where('start_time', '<', $request->to_date);
      }

      //検索ワード
      if(isset($request->search_word)){
        $search_words = explode(' ', $request->search_word);
        $items = $items->where(function($items)use($search_words){
          foreach($search_words as $_search_word){
            if(empty($_search_word)) continue;
            $_like = '%'.$_search_word.'%';
            $items->orWhere('remark','like',$_like);
          }
        });
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
      $param['item']['datetime'] = $param['item']['start_time'];
      $fields = [
        'start_time' => [
          'label' => '開始日時',
        ],
        'end_time' => [
          'label' => '終了日時',
        ],
        'status_name' => [
          'label' => 'ステータス',
        ],
        'place' => [
          'label' => '場所',
        ],
        'subject' => [
          'label' => '科目',
        ],
        'teacher_name' => [
          'label' => '講師',
        ],
        'student_name' => [
          'label' => '生徒',
        ],
      ];

      return view('components.page', [
        '_del' => $request->get('_del'),
        '_page_origin' => str_replace('_', '/', $request->get('_page_origin')),
        'fields'=>$fields])
        ->with($param);
    }
    /**
     * カレンダーステータス更新ページ
     *
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function status_update_page(Request $request, $id, $status)
    {
      if (!View::exists('calendars.'.$status)) {
          abort(404);
      }
      $param = $this->get_param($request, $id);

      //生徒・親の場合は、対象生徒指定があり、かつ、関係者である場合操作可能
      if($this->is_student_or_parent($param['user']->role)){
        $student = Student::where('id', $param['student_id'])->first();
        $res = $this->check_role($student->user_id);
        if(!$this->is_success_response($res)){
          abort(403);
        }
      }

      $fields = [
        'datetime' => [
          'label' => '日時',
        ],
        'detail' => [
          'label' => '詳細',
        ],
        'teacher_name' => [
          'label' => '講師',
        ],
        'student_name' => [
          'label' => '生徒',
        ],
      ];
      if(isset($param['item'])){
        $datetime = $param['item']['date'].' '.$param['item']['start'].'～'.$param['item']['end'];
        $param['item']['datetime'] = $datetime;
        $detail = '';
        $detail .= $param['item']['place'].'/';
        $detail .= $param['item']['subject'].'';
        $param['item']['detail'] = $detail;

        return view('calendars.'.$status, [
          '_page_origin' => str_replace('_', '/', $request->get('_page_origin')),
          'fields'=>$fields])
          ->with($param);
      }
      return abort(404);
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

      //生徒・親の場合は、対象生徒指定があり、かつ、関係者である場合操作可能
      if($this->is_student_or_parent($param['user']->role)){
        $student = Student::where('id', $param['student_id'])->first();
        $res = $this->check_role($student->user_id);
        if(!$this->is_success_response($res)){
          abort(403);
        }
      }

      $item = $param['item'];
      $res = $this->_status_update($param, $status);
      $slack_type = 'エラー';
      $slack_message = '更新エラー';

      $status_update_message = [
        'rest' => '休み連絡をしました。',
        'presence' => '授業予定を出席に更新しました。',
        'absence' => '授業予定を欠席に更新しました。',
      ];

      if($this->is_success_response($res)){
        $slack_type = 'info';
        $slack_message = $status_update_message[$status];
        if($status==="rest"){
          $this->rest_mail($param);
        }
      }
      $this->send_slack('カレンダーステータス更新['.$status.']:'.$slack_message.' / id['.$item['id'].']開始日時['.$item['start_time'].']終了日時['.$item['end_time'].']生徒['.$item['student_name'].']講師['.$item['teacher_name'].']', 'info', 'カレンダーステータス更新');
      if($status==="rest"){
        $this->rest_mail($param);
      }

      return $this->save_redirect($res, $param, $status_update_message[$status], str_replace('_', '/', $request->get('_page_origin')));
    }
    private function _status_update($param, $status){
      $item = $param['item'];
      try {
        DB::beginTransaction();
        //カレンダーステータス変更
        $this->model()->where('id', $item->id)->update(['status'=>$status]);
        //カレンダーメンバーステータス変更
        UserCalendarMember::where('calendar_id', $item->id)
            ->where('user_id', $param['user']->user_id)
            ->update(['status'=>$status]);
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

    /**
     * 休み連絡通知メール送信
     * @param  Array  $param
     * @return boolean
     */
    private function rest_mail($param){
      return  $this->_mail($param,
               'お休み連絡',
               'calendar_rest');

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
    private function _mail($param, $title, $template){
      $item = $param['item'];
      $login_user = $param['user'];
      $is_student = $this->is_student($login_user->role);
      $is_student_or_parent = $this->is_student_or_parent($login_user->role);
      $target_student_id = $param['student_id'];
      $role = $login_user->role;
      if($this->is_student_or_parent($login_user->role)){
        if(!is_numeric($target_student_id)){
          //生徒・保護者は対象の生徒設定がない場合NG
          return false;
        }
      }
      $members = UserCalendarMember::where('calendar_id', $item->id)->get();
      foreach($members as $member){
        $user = $member->user;
        $email = $user['email'];
        $user = $member->user->details();
        $send_type = 'teacher';
        $user_name = $user['name'];
        if($is_student_or_parent){
          if($this->is_student($user->role)){
            //生徒または保護者の操作、かつ、カレンダー対象者＝生徒の場合
            //確認用メールを送信
            if($target_student_id === $user->id){
              $send_type = "student";
              $parents = Student::where('id', $user->id)->first()->parents();
              $email = '';
              foreach($parents as $parent){
                $email = $parent->email.';';
              }
            }
            else {
              //それ以外の生徒にはメールしない
              continue;
            }
          }
        }
        else if(!$is_student_or_parent && $user->user_id===$login_user->user_id){
          //講師・事務の場合は自分あてにメールはださない（生徒・自分以外の講師あてにメールを出す）
          continue;
        }
        $this->send_mail($email,
         $title,
         [
         'user_name' => $user_name,
         'send_type' => $send_type,
         'item' => $item
         ],
         'text',
         $template);
      }
      return true;
    }
    private function get_students($param){
      $items = [];
      if($this->is_manager($param['user']->role)){
        $items = Student::whereRaw('students.user_id in (select id from users where status=0)')->get();
        foreach($items as $item){
          $item['id'] = $item['user_id'];
          $item['name'] = $item['name_last'].' '.$item['name_first'];
        }
      }
      else if($this->is_teacher($param['user']->role)){
        $items = ChargeStudent::where('teacher_id', $param['user']->id)->get();
        foreach($items as $item){
          $student = $item->student;
          $item['name'] = $student['name_last'].' '.$student['name_first'];
        }
      }
      return $items;
    }
    private function get_teachers($param){
      $items = [];
      if($this->is_manager($param['user']->role)){
        $items = Teacher::whereRaw('teachers.user_id in (select id from users where status=0)')->get();
      }
      return $items;
    }
    private function get_lectures($param){

      $items = Lecture::query();
      $items = $items->whereRaw('lectures.id in (select lecture_id from charge_students where teacher_id=?)', [$param['user']->id]);
      $items = $items->orderBy('lesson', 'asc')
                  ->orderBy('course', 'asc')
                  ->orderBy('subject', 'asc')->get();

      $ret = [];
      foreach($items as $item){
        $detail = $item->details();
        $item['name'] = $detail['name'];
        $lesson = $detail['lesson']['attribute_name'];
        if(!isset($ret[$lesson])){
          $ret[$lesson] = [];
        }
        $course = $detail['course']['attribute_name'];
        if(!isset($ret[$lesson][$course])){
          $ret[$lesson][$course] = [];
        }
        $subject = $detail['subject']['attribute_name'];
        if(!isset($ret[$lesson][$course][$subject])){
          $ret[$lesson][$course][$subject] = $item['id'];
        }
      }
      return $ret;
    }

    /**
     * 新規登録画面
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Request $request)
   {
      $param = $this->get_param($request);
      $param['students'] = $this->get_students($param);
      $param['teachers'] = $this->get_teachers($param);
      $param['lectures'] = $this->get_lectures($param);

      $param['item'] = [
        'lesson_time' => $request->get('lesson_time'),
        'start_date' => $request->get('start_date'),
        'start_hours' => $request->get('start_hours'),
        'start_minutes' => $request->get('start_minutes'),
      ];
      return view($this->domain.'.create',
        ['_page_origin' => str_replace('_', '/', $request->get('_page_origin')),
         'error_message' => ''])
        ->with($param);
    }

}
