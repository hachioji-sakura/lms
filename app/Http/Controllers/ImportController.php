<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\GeneralAttribute;
use App\Models\Lecture;
use App\Models\Textbook;
use App\Models\TextbookTag;
use App\Models\Publisher;
use App\Models\Supplier;
use App\Models\TextbookSale;

use App\Models\Student;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\UserTag;
use App\Models\ChargeStudent;

use App\Models\UserCalendar;
use App\Models\UserCalendarMember;

use Illuminate\Http\Request;
use DB;
class ImportController extends UserController
{
    //事務管理システム側の情報
    //API URL: domain+endpoint+.php?query_string
    public $logic_name = '事務管理システム-データ取り込み';
    public $api_domain = 'https://hachiojisakura.com/sakura-api';
    public $api_endpoint = [
      'courses' =>  'api_get_course',
      'subjects' =>  'api_get_subject',
      'lessons' =>  'api_get_lesson',
      'lectures' =>  'api_get_lecture',
      'students' =>  'api_get_student',
      'teachers' =>  'api_get_teacher',
      'textbooks' =>  'api_get_material',
      'charge_students' =>  'api_get_teacherstudent',
      'calendars' => 'api_get_calendar',
    ];
    //API auth token
    public $token = '7511a32c7b6fd3d085f7c6cbe66049e7';


    /**
     * 事務管理システムAPI（GET）
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $object
     * @return Json
     */
    public function index(Request $request, $object)
    {
      if(!array_key_exists($object, $this->api_endpoint)){
        return $this->bad_request();
      }
      $url = $this->api_domain.'/'.$this->api_endpoint[$object].'.php';
      $res = $this->call_api($request, $url);
      return $res;
    }
    /**
     * 事務管理システムAPI - import（POST）
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $object | 対象データ
     * @return Json
     */
    public function import(Request $request, $object)
    {
      set_time_limit(600);
      if($object=="all"){
        $objects = ['courses',
          'lessons',
          'subjects',
          'lectures',
          'students',
          'teachers',
          'textbooks',
          'charge_students',
          'calendars',
        ];
        foreach($objects as $_object){
          $res = $this->_import($request, $_object);
          if(!$this->is_success_response($res)){
            break;
          }
        }
      }
      else if($object=="attributes"){
        $objects = ['courses',
          'lessons',
          'subjects',
          'lectures',
          'textbooks',
        ];
        foreach($objects as $_object){
          $res = $this->_import($request, $_object);
          if(!$this->is_success_response($res)){
            break;
          }
        }
      }
      else if($object=="users"){
        $objects = [
          'students',
          'teachers',
          'charge_students',
        ];
        foreach($objects as $_object){
          $res = $this->_import($request, $_object);
          if(!$this->is_success_response($res)){
            break;
          }
        }
      }
      else {
        $res = $this->_import($request, $object);
      }
      return $this->send_json_response($res);
    }

    /**
     * 事務管理システムAPI - importロジック
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $object
     * @return Json
     */
    private function _import(Request $request, $object)
    {
        $this->send_slack('import start['.$object.']', 'info', $this->logic_name);

        if(!array_key_exists($object, $this->api_endpoint)){
          return $this->bad_request();
        }
        $url = $this->api_domain.'/'.$this->api_endpoint[$object].'.php';
        $this->send_slack('import call_api['.$url.']', 'info', $this->logic_name);
        $res = $this->call_api($request, $url);
        if(!$this->is_success_response($res)){
          return $this->error_response('api error['.$res['message'].']', $url);
        }
        $items = $res['data'];
        switch($object){
          case 'courses':
            $res = $this->general_attributes_import($items, 'course', 'course_id', 'course_name');
            break;
          case 'lessons':
            $res = $this->general_attributes_import($items, 'lesson', 'lesson_id', 'lesson_name');
            break;
          case 'subjects':
            $res = $this->general_attributes_import($items, 'subject', 'subject_id', 'subject_name');
            break;
          case 'lectures':
            $res = $this->lectures_import($items);
            break;
          case 'students':
            $res = $this->students_import($items);
            break;
          case 'teachers':
            $res = $this->teachers_import($items);
            break;
          case 'charge_students':
            $res = $this->charge_students_import($items);
            break;
          case 'textbooks':
            $res = $this->textbooks_import($items);
            break;
          case 'calendars':
            $res = $this->calendars_import($items);
            break;
        }
        if(!$this->is_success_response($res)){
          $this->send_slack($res['message'], 'error', $this->logic_name);
          $this->send_slack($res['description'], 'error', $this->logic_name);
        }
        else {
          $this->send_slack($res['message'], 'success', $this->logic_name);
          $this->send_slack($res['description'], 'success', $this->logic_name);
        }
        $this->send_slack('import end['.$object.']', 'info',  $this->logic_name);

        return $res;
    }

    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function general_attributes_import($items, $key_name, $id_column, $idname_column){
      try {
        DB::beginTransaction();
        $c=0;
        foreach($items as $item){
          if($this->store_general_attribute($key_name, $item[$id_column], $item[$idname_column])) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, $key_name.'['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function students_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_student($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function teachers_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_teacher($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function charge_students_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        echo 'count='.count($items).'\n';
        foreach($items as $item){
          if($this->store_charge_student($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function textbooks_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_textbook($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function calendars_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_calendar($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function lectures_import($items){
      try {
        DB::beginTransaction();
        Lecture::truncate();
        $c = 0;
        foreach($items as $item){
          if($this->store_lecture($item)) $c++;
        }
        DB::commit();
        return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
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
     * 講師情報登録
     * @param array $item
     * @return boolean
     */
    private function store_teacher($item){
      $item['email'] = $item['mail_address'];
      if(empty($item['email'])) $item['email'] = $item['teacher_no'];
      $item['image_id'] = 3;
      $item['password'] = 'sakusaku';
      $item['status'] = 1; //インポートしただけで、アカウント通知が必要な状況
      if(intval($item['del_flag'])===2){
        $item['status'] = 9;
      }

      $user = User::where('email', $item['email'])->first();
      $user_id = 0;
      if(!isset($user)){
        //認証情報登録
        $res = $this->user_create([
          'name' => $item['teacher_no'],
          'password' => $item['password'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)){
          //講師情報登録
          $Teacher = new Teacher;
          $_item = $Teacher->create([
            'name' => $item['teacher_name'],
            'kana' => $item['teacher_furigana'],
            'user_id' => $res['data']->id,
            'create_user_id' => 1
          ]);
          $user_id = $res['data']->id;
        }
        else {
          $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['teacher_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $user_id = $user->id;
        $teacher = Teacher::where('user_id', $user_id)->first();
        if(!isset($teacher)){
          $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['teacher_no']."認証あり / 講師情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
        $teacher->update([
          'name' => $item['teacher_name'],
          'kana' => $item['teacher_furigana'],
        ]);
        $user->update([
          'status' => $item['status'],
        ]);
      }
      UserTag::where('user_id',$user_id)->delete();
      //講師属性登録
      $this->store_user_tag($user_id, 'teacher_no', $item['teacher_no'], false);
      if($item['lesson_id']!='0') $this->store_user_tag($user_id, 'lesson', $item['lesson_id']);
      if($item['lesson_id2']!='0') $this->store_user_tag($user_id, 'lesson', $item['lesson_id2']);
      return true;
    }
    /**
     * 生徒情報登録
     * @param array $item
     * @return boolean
     */
    private function store_student($item){
      $item['email'] = $item['mail_address'];
      if(is_numeric($item['jyukensei']) && $item['jyukensei']=='1'){
        $item['jyukensei'] = 'jyuken';
      }
      else {
        $item['jyukensei'] = '';
      }
      if(is_numeric($item['fee_free']) && $item['fee_free']=='1'){
        $item['fee_free'] = 'fee_free';
      }
      else {
        $item['fee_free'] = '';
      }

      if(!is_numeric($item['gender'])){
        $item['image_id'] = 4;
        $item['gender'] = 3;
      }
      else {
        $item['gender'] = integer($item['gender']);
        $item['image_id'] = $item['gender'];
      }

      if(strlen($item['birth_year'])===4 && $item['birth_month']!='0' && $item['birth_day'] != '0'){
        $item['_birth_day'] = $item['birth_year'].'-'.$item['birth_month'].'-'.$item['birth_day'];
      }
      else {
        //仮の生年月日
        $item['_birth_day'] = '1900-01-01';
      }

      $item['status'] = 1; //インポートしただけで、アカウント通知が必要な状況
      if(intval($item['del_flag'])===2){
        $item['status'] = 9;
      }

      if(empty($item['email'])){
        if($item['status']!==9){
          //削除してない生徒で、メールアドレスがない場合は通知
          $this->send_slack("事務管理システム:no=".$item['student_no']."メールアドレス設定なし", 'error', $this->logic_name);
        }
        $item['email'] = 'email_'.$item['student_no'];
      }

      $item['password'] = 'sakusaku';
      $kana = $item['student_furigana'];
      $item['kana_last'] = '';
      $item['kana_first'] = '';
      if(!empty($item['student_furigana'])){
          $kanas = explode(' ', $item['student_furigana'].' ');
          $item['kana_last'] = $kanas[0];
          $item['kana_first'] = $kanas[1];
      }

      $parent_user = User::where('email', $item['email'])->first();
      $parent = null;
      if(!isset($parent_user)){
        //認証情報登録(保護者として登録）
        $res = $this->user_create([
          'name' => $item['family_name'].'様',
          'password' => $item['password'],
          'email' => $item['email'],
          'image_id' => 4,
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)){
          //保護者情報登録
          $parent_user = $res['data'];
          $StudentParent = new StudentParent;
          $parent = $StudentParent->create([
            'name_last' => $item['family_name'],
            'name_first' => $item['first_name'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'user_id' => $parent_user->id,
            'create_user_id' => 1,
          ]);
        }
        else {
          $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $parent = StudentParent::where('user_id', $parent_user->id)->first();
        //認証情報存在：既存更新
       if(!isset($parent)){
         $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['student_no']."認証あり / 保護者情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
         return false;
       }
       $parent->update([
         'name_last' => $item['family_name'],
         'name_first' => $item['first_name'],
         'kana_last' => $item['kana_last'],
         'kana_first' => $item['kana_first'],
       ]);
       if($item['status']===9){
         //削除時のみ更新
         $parent_user->update([
           'status' => $item['status'],
         ]);
       }
      }

      $user = User::tag('student_no', $item['student_no'])->first();
      $student = null;
      if(!isset($user)){
        //認証情報なし：新規登録
        $res = $this->user_create([
          'name' => $item['student_no'],
          'password' => $item['password'],
          'email' => $item['student_no'],
          'image_id' => $item['image_id'],
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)){
          //生徒情報登録
          $user = $res['data'];
          $Student = new Student;
          $student = $Student->create([
            'name_last' => $item['family_name'],
            'name_first' => $item['first_name'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'birth_day' => $item['_birth_day'],
            'gender' => $item['gender'],
            'user_id' => $user->id,
            'create_user_id' => 1,
          ]);
        }
        else {
          $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
         //認証情報存在：既存更新
        $student = Student::where('user_id', $user->id)->first();
        if(!isset($student)){
          $this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['student_no']."認証あり / 生徒情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
        $student->update([
          'name_last' => $item['family_name'],
          'name_first' => $item['first_name'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
          'birth_day' => $item['_birth_day'],
          'gender' => $item['gender'],
        ]);
        if($item['status']===9){
          //削除時のみ更新
          $user->update([
            'status' => $item['status'],
          ]);
        }
      }

      //$this->send_slack("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録！:email=".$user->email." / name=".$user->name, 'info', $this->logic_name);
      //家族情報（関連性）の登録
      StudentRelation::where('student_id',$student->id)->delete();
      $StudentRelation = new StudentRelation;
      $StudentRelation->create([
        'student_id' => $student->id,
        'student_parent_id' => $parent->id,
        'create_user_id' => 1,
      ]);
      //生徒属性登録
      UserTag::where('user_id', $user->id)->delete();
      //生徒種別：ほとんどが3=生徒なので取得不要と思う、2=職員？、1=本部？
      //$this->store_user_tag($user->id, 'student_kind', $item['student_kind']);
      $this->store_user_tag($user->id, 'student_no', $item['student_no'], false);
      $this->store_user_tag($user->id, 'grade', $item['grade']);
      $this->store_user_tag($user->id, 'grade_adj', $item['grade_adj']);
      $this->store_user_tag($user->id, 'student_type', $item['fee_free']);
      $this->store_user_tag($user->id, 'student_type', $item['jyukensei']);
      return true;
    }
    /**
     * 担当生徒登録
     * @param array $item
     * @return boolean
     */
    private function store_charge_student($item){
      if(empty($item['student_no']) || intval($item['student_no'])==0) return false;
      if(empty($item['teacher_no']) || intval($item['teacher_no'])==0) return false;

      $student = User::tag('student_no', $item['student_no'])->first();
      if(!isset($student)){
        $this->send_slack("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $student = $student->student;

      $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
      if(!isset($teacher)){
        $this->send_slack("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $teacher = $teacher->teacher;

      $lecture = Lecture::where('lecture_id_org',$item['lecture_id'])->first();
      if(!isset($lecture)){
        $this->send_slack("事務管理システム:lecture_id=".$item['lecture_id']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }

      $items = ChargeStudent::where('student_id', $student->id)
      ->where('teacher_id', $teacher->id)
      ->where('lecture_id', $lecture->id)->first();

      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      ChargeStudent::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'lecture_id' => $lecture->id,
        'create_user_id' => 1
      ]);
      return true;
    }
    /**
     * カレンダー登録
     * @param array $item
     * @return boolean
     */
    private function store_calendar($item){
      $student = User::tag('student_no', $item['student_no'])->first();
      if(!isset($student)){
        $this->send_slack("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $student = $student->student;

      $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
      if(!isset($teacher)){
        $this->send_slack("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $teacher = $teacher->teacher;

      $remark = $item['comment'];
      $lecture = Lecture::where('lesson',$item['lesson'])
        ->where('course',$item['course'])
        ->where('subject',$item['subject'])
        ->first();
      if(!isset($lecture)){
        //存在しないレクチャー
        $this->send_slack("事務管理システム:lesson=".$item['lesson']."/course=".$item['course']."/subject=".$item['subject']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $remark.='[lesson='.$item['lesson'].']';
      $remark.='[course='.$item['course'].']';
      $remark.='[subject='.$item['subject'].']';
      $exchanged_calendar_id = 0;
      //status=1.仮付きの場合：new / 2.仮なし:fix / 3.休み1 or 休み2:rest / 4.出席 : presence 5.欠席:absence
      $status= 'fix';
      if(!empty($item['yasumi'])){
        //TODO :以下の項目をどうにかしたい
        $_attr = $this->get_save_general_attribute('absence_type', '', $item['yasumi']);
        $yasumi = $_attr->attribute_value;
        $remark.='[yasumi='.$item['yasumi'].']';
        if($item['yasumi']=='休み1' || $item['yasumi']=='休み2'){
          $status = 'cancel';
        }
        else if($item['yasumi']=='振替'){
          $exchanged_calendar_id = -1;
        }
      }

      $_attr = $this->get_save_general_attribute('place', '', $item['calendar']);
      $place = $_attr->attribute_value;

      $calendar_id = 0;
      $items = UserCalendar::where('start_time',$item['start'])
        ->where('end_time',$item['end'])
        ->where('user_id',$teacher->user_id)->first();
      if(isset($items)){
        //すでに存在する場合は更新する
        $items->update([
          'lecture_id' => $lecture->id,
          'remark' => $remark,
          'status' => $status,
          'place' => $place,
        ]);
        $calendar_id = $items->id;
      }
      else {
        $items = UserCalendar::create([
          'start_time' => $item['start'],
          'end_time' => $item['end'],
          'user_id' => $teacher->user_id,
          'lecture_id' => $lecture->id,
          'remark' => $remark,
          'status' => $status,
          'place' => $place,
          'create_user_id' => 1
        ]);
        $calendar_id = $items->id;
      }
      UserCalendarMember::where('calendar_id',$calendar_id)->delete();

      /*誰の休みか？
      course=1 / マンツー
        休み１：月１の振替＝生徒起因 / それ以外講師
        休み２：ほぼ生徒
      course=2 / グループ
        休み１：ほぼ生徒
        休み２：ほぼ生徒
      course=3 / ファミリー（マンツーと同じ？）
      */
      $teacher_status = $status;
      $student_status = $status;
      /*
      if($item['course']=='1' || $item['course']=='3'){
        if($item['yasumi']=='休み1'){
        }
        else if($item['yasumi']=='休み2'){
        }
      }
      else if($item['course']=='2'){
        if($item['yasumi']=='休み1'){
        }
        else if($item['yasumi']=='休み2'){
        }
      }
      */
      UserCalendarMember::create([
        'calendar_id' => $calendar_id,
        'status' => $teacher_status,
        'user_id' => $teacher->user_id,
        'create_user_id' => 1
      ]);

      UserCalendarMember::create([
        'calendar_id' => $calendar_id,
        'user_id' => $student->user_id,
        'status' => $student_status,
        'create_user_id' => 1
      ]);

      $charge_student = ChargeStudent::where('student_id',$student->id)
        ->where('teacher_id' ,$teacher->id)
        ->where('lecture_id' ,$lecture->id)->first();
      if(!isset($charge_student)){
        //担当生徒データが存在しない
        //$this->send_slack("カレンダーID[".$calendar_id."]  担当生徒データが存在しない / 講師[".$teacher->id."]生徒[".$student->id."]レクチャー[".$lecture->id."]", 'warning', $this->logic_name);
      }
      return true;
    }
    /**
     * カレンダーアーカイブ登録
     * @param array $item
     * @return boolean
     */
    public function archive_calendar($item){
      DB::statement('alter table users auto_increment = 1');

      return true;
    }

    /**
     * ユーザータグ登録
     * @param array $item
     * @return boolean
     */
    private function store_user_tag($user_id, $key, $val, $add_attribute=true){
      if(empty($user_id)) return false;
      if(empty($key)) return false;
      if(empty($val)) return false;
      if($add_attribute===true){
        //汎用属性に登録
        $this->store_general_attribute($key, $val, $val);
      }
      $items = UserTag::where('user_id', $user_id)
        ->where('tag_key', $key)
        ->where('tag_value', $val)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $key,
        'tag_value' => $val,
        'create_user_id' => 1
      ]);
      return true;
    }
    /**
     * textbookタグ登録
     * @param array $item
     * @return boolean
     */
    private function store_textbook_tag($textbook_id, $key, $val){

      $items = TextbookTag::where('textbook_id', $textbook_id)
        ->where('tag_key', $key)
        ->where('tag_value', $val)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }
      TextbookTag::create([
        'textbook_id' => $textbook_id,
        'tag_key' => $key,
        'tag_value' => $val,
        'create_user_id' => 1
      ]);
      return true;
    }
    /**
     * 属性マスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_general_attribute($key, $value, $name){
      $items = GeneralAttribute::where('attribute_key', $key)
        ->where('attribute_value', $value)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }
      GeneralAttribute::create([
        'attribute_key' => $key,
        'attribute_value' => $value,
        'attribute_name' => $name,
        'create_user_id' => 1,
        ]);
      return true;
    }
    /**
     * 属性マスタ登録&取得
     * @param array $item
     * @return boolean
     */
    private function get_save_general_attribute($key, $value, $name){
      $items = GeneralAttribute::where('attribute_key', $key);
      if(!empty($value)) $items = $items->where('attribute_value', $value);
      if(!empty($name)) $items = $items->where('attribute_name', $name);
      $items = $items->first();

      if(isset($items) && !empty($items->attribute_value)){
        //すでに存在する場合は保存しない
        return $items;
      }
      if(empty($value)) $value = $name;
      return GeneralAttribute::create([
        'attribute_key' => $key,
        'attribute_value' => $value,
        'attribute_name' => $name,
        'create_user_id' => 1,
        ]);
    }
    /**
     * 教科書マスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_textbook($item){
      //出版社の登録
      $publisher_id = 0;
      if(!empty($item['publisher_name'])){
        $items = Publisher::where('url', $item['publisher_id'])->first();
        if(isset($items)){
          $publisher_id = $items->id;
        }
        else {
          //存在しなければ追加
          $items = Publisher::create([
              'name' => $item['publisher_name'],
              'url' => $item['publisher_id'],
              'create_user_id' => 1
          ]);
          $publisher_id = $items->id;
        }
      }
      //販売会社の登録
      $supplier_id = 0;
      if(!empty($item['supplier_name'])){
        $items = Supplier::where('url', $item['supplier_id'])->first();
        if(isset($items)){
          $supplier_id = $items->id;
        }
        else {
          //存在しなければ追加
          $items = Supplier::create([
              'name' => $item['supplier_name'],
              'url' => $item['supplier_id'],
              'publisher_id' => $publisher_id,
              'create_user_id' => 1
          ]);
          $supplier_id = $items->id;
        }
      }
      $items = Textbook::where('url', $item['id'])->first();
      $textbook_id = 0;
      $price = 0;
      $list_price = 0;
      if(isset($items)){
        $textbook_id = $items->id;
        $price = $items->list_price;
        $list_price = $items->list_price;
      }
      else {
        if(empty($item['explain'])) $item['explain'] = '';
        if(empty($item['teika_price'])) $item['teika_price'] = 0;
        if(empty($item['publisher_price'])) $item['publisher_price'] = 0;
        if(empty($item['tewatashi_price1'])) $item['tewatashi_price1'] = 0;
        if(empty($item['tewatashi_price2'])) $item['tewatashi_price2'] = 0;
        if(empty($item['tewatashi_price3'])) $item['tewatashi_price3'] = 0;
        $items = Textbook::create([
          'name' => $item['name'],
          'selling_price' => str_replace(',','', $item['publisher_price']),
          'list_price' => str_replace(',','', $item['teika_price']),
          'price1' => str_replace(',','', $item['tewatashi_price1']),
          'price2' => str_replace(',','', $item['tewatashi_price2']),
          'price3' => str_replace(',','', $item['tewatashi_price3']),
          'image_id' => 0,
          'url' => $item['id'],
          'explain' => $item['explain'],
          'publisher_id' => $publisher_id,
          'create_user_id' => 1,
        ]);
        $textbook_id = $items->id;
        $price = $items->list_price;
        $list_price = $items->list_price;
      }
      //販売データの登録
      //出版社の登録
      $items = TextbookSale::where('textbook_id',$textbook_id)
        ->where('supplier_id',$supplier_id)->first();
      if(!isset($items)){
        //存在しなければ追加
        $items = TextbookSale::create([
            'textbook_id' => $textbook_id,
            'supplier_id' => $supplier_id,
            'price' => $price,
            'list_price' => $list_price,
        ]);
      }
      TextbookTag::where('textbook_id', $textbook_id)->delete();
      //教科書タグの登録（科目、レベル、学年）
      if(!empty($item['subject'])){
        $_attr = $this->get_save_general_attribute('subject', '', $item['subject']);
        $this->store_textbook_tag($textbook_id, 'subject', $_attr->attribute_value);
      }
      if(!empty($item['level'])){
        $_attr = $this->get_save_general_attribute('level', '', $item['level']);
        $this->store_textbook_tag($textbook_id, 'level', $_attr->attribute_value);
      }
      if(!empty($item['grade'])){
        $_attr = $this->get_save_general_attribute('grade', '', $item['grade']);
        $this->store_textbook_tag($textbook_id, 'grade', $_attr->attribute_value);
      }

      return true;
    }

    /**
     * レクチャーマスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_lecture($item){
      $subject = GeneralAttribute::subject($item['subject_id'])->first();
      if(!isset($subject)){
        return false;
      }
      $lesson = GeneralAttribute::lesson($item['lesson_id'])->first();
      if(!isset($lesson)){
        return false;
      }
      $course = GeneralAttribute::course($item['course_id'])->first();
      if(!isset($course)){
        return false;
      }

      $items = Lecture::where('lesson', $lesson->attribute_value)
        ->where('course',$course->attribute_value)
        ->where('subject',$subject->attribute_value)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }
      else {
        //存在しなければ追加
        $items = Lecture::create([
          'lecture_id_org' => intval($item['lecture_id']),
          'lesson' =>  $lesson->attribute_value,
          'course' =>  $course->attribute_value,
          'subject' =>  $subject->attribute_value,
        ]);
      }

      return true;
    }

}
