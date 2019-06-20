<?php

namespace App\Http\Controllers;
use App\Models\Lecture;
use App\Models\GeneralAttribute;

use Illuminate\Http\Request;
use DB;
class LectureController extends UserController
{
  public $domain = "lectures";
  public $table = "lectures";
  public $domain_name = "レクチャー";
  /**
   * このdomainで管理するmodel
   *
   * @return model
   */
  public function model(){
    return Lecture::query();
  }

  public function api_index(Request $request)
  {
    $_table = $this->search($request);
    return $this->api_response(200, "", "", $_table);

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
   abort(404);
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
    $ret = [
      'domain' => $this->domain,
      'domain_name' => $this->domain_name,
      'user' => $user,
      'mode'=>$request->mode,
      'search_word'=>$request->search_word,
      'attributes' => $this->attributes(),
    ];
    if(empty($user)){
      //ログインしていない
      abort(419);
    }
    return $ret;
  }

  /**
   * 検索～一覧
   *
   * @param  \Illuminate\Http\Request  $request
   * @return [Collection, field]
   */
  public function search2(Request $request)
  {
    $param = $this->get_param($request);
    $sql = <<<EOT
    select
     l.id as lecture_id,
     l.lesson as lesson,
     lesson.attribute_name as lesson_name,
     l.course as course,
     course.attribute_name as course_name,
     l.subject as subject,
     subject.attribute_name as subject_name
    from
      lectures l
      left join general_attributes lesson on lesson.attribute_key = 'lesson' and lesson.attribute_value = l.lesson
      left join general_attributes subject on subject.attribute_key = 'subject' and subject.attribute_value = l.subject
      left join general_attributes course on course.attribute_key = 'course' and course.attribute_value = l.course
EOT;
    if($this->is_teacher($param['user']->role)){
      $sql .= " where l.lesson in (select tag_value from user_tags where user_id=? and tag_key='lesson')";
    }
    $sql .= " order by lesson.sort_no, course.sort_no, subject.sort_no";
    $items = DB::select($sql, [$param['user']->user_id]);
    return $items;
  }
  public function search(Request $request)
  {
    $param = $this->get_param($request);
    return $this->_search($request);
  }
  public function _search(Request $request)
  {
    $user = $this->login_details($request);
    $teacher_id = 0;
    $student_id = 0;
    if($request->has('student_id')){
      $student_id = $request->get('student_id');
    }
    if($request->has('teacher_id')){
      $teacher_id = $request->get('teacher_id');
    }

    $items = $this->model();
    if($this->is_student_or_parent($user->role) && $student_id === 0){
      return $this->forbidden();
    }

    if($student_id > 0 && $teacher_id > 0){
      $items = $items->findChargeStudentLesson($teacher_id, $student_id);
    }
    else if($teacher_id > 0){
      $items = $items->findChargeLesson($teacher_id);
    }

    if($request->has('lesson')){
      $items = $items->where('lesson' ,$request->get('lesson'));
    }

    if($request->has('course')){
      $items = $items->where('course' ,$request->get('course'));
    }

    $items = $items->orderBy('lesson', 'asc')->orderBy('course', 'asc')->orderBy('subject', 'asc');
    $items = $items->get();
    foreach($items as $key => $item){
      $items[$key] = $item->details();
    }
    return $items->toArray();
  }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
