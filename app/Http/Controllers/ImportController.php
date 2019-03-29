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
use App\Models\Manager;
use App\Models\UserTag;
use App\Models\ChargeStudent;

use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;

use Illuminate\Http\Request;
use DB;
class ImportController extends UserController
{
    //事務管理システム側の情報
    //API URL: domain+endpoint+.php?query_string
    public $logic_name = '事務管理システム-データ取り込み';
    public $api_domain = 'https://hachiojisakura.com/sakura-api';
    public $api_endpoint = [
      'works' => 'api_get_work_explanation',
      'places' =>  'api_get_place_name',
      'courses' =>  'api_get_course',
      'subjects' =>  'api_get_subject',
      'lessons' =>  'api_get_lesson',
      'lectures' =>  'api_get_lecture',
      'students' =>  'api_get_student',
      'teachers' =>  'api_get_teacher',
      'managers' =>  'api_get_staff',
      'textbooks' =>  'api_get_material',
      'charge_students' =>  'api_get_teacherstudent', //TODO:使わなくなる
      'repeat_schedules' =>  'api_get_repeat_schedule',
      'calendars' => 'api_get_calendar', //TODO:使わなくなる
      'schedules' => 'api_get_onetime_schedule',
      'attends' => 'api_get_attend', //TODO:使わなくなる
    ];
    public $api_update_endpoint = [
      'schedules' => 'api_update_onetime_schedule',
    ];
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
      if($object==='concealment'){
        $res = $this->concealment();
        return $res;
      }
      else if($object=='all'){
        $objects = [
          'works',
          'places',
          'courses',
          'lessons',
          'subjects',
          'lectures',
          'students',
          'teachers',
          'managers',
          'textbooks',
          'repeat_schedules',
          'schedules',
          'attends',
        ];
        foreach($objects as $_object){
          $res = $this->_import($request, $_object);
          if(!$this->is_success_response($res)){
            break;
          }
        }
      }
      else if($object=='attributes'){
        $objects = [
          'works',
          'places',
          'courses',
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
      else if($object=='users'){
        $objects = [
          'students',
          'teachers',
          'managers',
          'repeat_schedules',
          'schedules',
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
        @$this->remind('import start['.$object.']', 'info', $this->logic_name);

        if(!array_key_exists($object, $this->api_endpoint)){
          return $this->bad_request();
        }
        $url = $this->api_domain.'/'.$this->api_endpoint[$object].'.php';
        @$this->remind('import call_api['.$url.']', 'info', $this->logic_name);
        $res = $this->call_api($request, $url);
        if(!$this->is_success_response($res)){
          return $this->error_response('api error['.$res.']', $url);
        }
        $items = $res['data'];
        switch($object){
          case 'places':
            $this->logic_name = "場所マスタ取り込み";
            $res = $this->general_attributes_import($items, 'place', 'id', 'name');
            break;
          case 'works':
            $this->logic_name = "作業種別マスタ取り込み";
            $res = $this->general_attributes_import($items, 'work', 'id', 'explanation');
            break;
          case 'courses':
            $this->logic_name = "コースマスタ取り込み";
            $res = $this->general_attributes_import($items, 'course', 'course_id', 'course_name');
            break;
          case 'lessons':
            $this->logic_name = "レッスンマスタ取り込み";
            $res = $this->general_attributes_import($items, 'lesson', 'lesson_id', 'lesson_name');
            break;
          case 'subjects':
            $this->logic_name = "科目マスタ取り込み";
            $res = $this->general_attributes_import($items, 'subject', 'subject_id', 'subject_name');
            break;
          case 'lectures':
            $this->logic_name = "レクチャマスタ取り込み";
            $res = $this->lectures_import($items);
            break;
          case 'students':
            $this->logic_name = "生徒取り込み";
            $res = $this->students_import($items);
            break;
          case 'teachers':
            $this->logic_name = "講師取り込み";
            $res = $this->teachers_import($items);
            break;
          case 'managers':
            $this->logic_name = "事務取り込み";
            $res = $this->managers_import($items);
            break;
          case 'charge_students':
            $this->logic_name = "担当生徒取り込み";
            $res = $this->charge_students_import($items);
            break;
          case 'repeat_schedules':
            $this->logic_name = "繰り返しスケジュール取り込み";
            $res = $this->repeat_schedules_import($items);
            break;
          case 'textbooks':
            $this->logic_name = "参考書データ取り込み";
            $res = $this->textbooks_import($items);
            break;
          case 'calendars':
            $this->logic_name = "カレンダーデータ取り込み";
            $res = $this->calendars_import($items);
            break;
          case 'schedules':
            $this->logic_name = "カレンダーデータ取り込み";
            $res = $this->schedules_import($items);
            break;
          case 'attends':
            $this->logic_name = "出席データ取り込み";
            $res = $this->attends_import($items);
            break;
        }
        if(!$this->is_success_response($res)){
          @$this->remind($res['message'], 'error', $this->logic_name);
          @$this->remind($res['description'], 'error', $this->logic_name);
        }
        else {
          @$this->remind($res['message'], 'success', $this->logic_name);
          @$this->remind($res['description'], 'success', $this->logic_name);
        }
        @$this->remind('import end['.$object.']', 'info',  $this->logic_name);

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
    private function managers_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_manager($item)) $c++;
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
    private function repeat_schedules_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_repeat_schedule($item)) $c++;
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
    private function schedules_import($items){
      $c = 0;
      foreach($items as $item){
        if($this->store_schedule($item)) $c++;
      }
      return $this->api_response(200, __FUNCTION__, 'count['.$c.']');
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_schedule($item)) $c++;
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
    private function attends_import($items){
      try {
        DB::beginTransaction();
        $c = 0;
        foreach($items as $item){
          if($this->store_attend($item)) $c++;
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
     * 事務情報登録
     * @param array $item
     * @return boolean
     */
    private function store_manager($item){
      if(empty($item['staff_no']) || intval($item['staff_no'])==0){
        if(empty($item['staff_id']) || intval($item['staff_id'])==0){
          return false;
        }
        //TODO : ネーミング問題、_idだったり,_noだったりする
        //後続はstaff_noで統一
        $item['staff_no'] = $item['staff_id'];
      }
      $item['email'] = $item['mail_address'];
      if(empty($item['email'])) $item['email'] = $item['staff_no'];
      $item['image_id'] = 4;
      $item['password'] = 'sakusaku';
      $item['status'] = 1; //インポートしただけで、アカウント通知が必要な状況
      if(isset($item['del_flag']) && intval($item['del_flag'])===2){
        $item['status'] = 9;
      }

      $item['kana_last'] = '';
      $item['kana_first'] = '';
      if(!empty($item['staff_furigana'])){
          $kanas = explode(' ', $item['staff_furigana'].' ');
          $item['kana_last'] = $kanas[0];
          $item['kana_first'] = $kanas[1];
      }

      $item['name_last'] = '';
      $item['name_first'] = '';
      if(!empty($item['staff_name'])){
          $names = explode(' ', $item['staff_name'].' ');
          $item['name_last'] = $names[0];
          $item['name_first'] = $names[1];
      }

      $user = User::where('email', $item['email'])->first();
      $user_id = 0;
      if(!isset($user)){
        //認証情報登録
        $res = $this->user_create([
          'name' => $item['staff_no'],
          'password' => $item['password'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)){
          //講師情報登録
          $_item = Manager::create([
            'name_last' => $item['name_last'],
            'name_first' => $item['name_first'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'user_id' => $res['data']->id,
            'create_user_id' => 1
          ]);
          $user_id = $res['data']->id;
        }
        else {
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['staff_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $user_id = $user->id;
        $manager = Manager::where('user_id', $user_id)->first();
        if(!isset($manager)){
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['staff_no']."認証あり / 講師情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
        $manager->update([
          'name_last' => $item['name_last'],
          'name_first' => $item['name_first'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
        ]);
        $user->update([
          'status' => $item['status'],
        ]);
      }
      UserTag::where('user_id',$user_id)->delete();
      //事務属性登録
      $this->store_user_tag($user_id, 'manager_no', $item['staff_no'], false);
      return true;
    }

    /**
     * 講師情報登録
     * @param array $item
     * @return boolean
     */
    private function store_teacher($item){
      $item['teacher_no'] = $this->get_id_value('teacher', $item);
      $item['email'] = $item['mail_address'];
      if(empty($item['email'])) $item['email'] = $item['teacher_no'];
      $item['image_id'] = 3;
      $item['password'] = 'sakusaku';
      $item['status'] = 1; //インポートしただけで、アカウント通知が必要な状況
      if(isset($item['del_flag']) && intval($item['del_flag'])===2){
        $item['status'] = 9;
      }

      $item['kana_last'] = '';
      $item['kana_first'] = '';
      if(!empty($item['teacher_furigana'])){
          $kanas = explode(' ', $item['teacher_furigana'].' ');
          $item['kana_last'] = $kanas[0];
          $item['kana_first'] = $kanas[1];
      }

      $item['name_last'] = '';
      $item['name_first'] = '';
      if(!empty($item['teacher_name'])){
          $names = explode(' ', $item['teacher_name'].' ');
          $item['name_last'] = $names[0];
          $item['name_first'] = $names[1];
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
            'name_last' => $item['name_last'],
            'name_first' => $item['name_first'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'user_id' => $res['data']->id,
            'create_user_id' => 1
          ]);
          $user_id = $res['data']->id;
        }
        else {
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['teacher_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $user_id = $user->id;
        $teacher = Teacher::where('user_id', $user_id)->first();
        if(!isset($teacher)){
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['teacher_no']."認証あり / 講師情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
        $teacher->update([
          'name_last' => $item['name_last'],
          'name_first' => $item['name_first'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
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
      $item['student_no'] = $this->get_id_value('student', $item);
      $item['student_no'] = intval($item['student_no']);

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
      if(isset($item['del_flag']) && intval($item['del_flag'])===2){
        $item['status'] = 9;
      }

      if(empty($item['email'])){
        if($item['status']!==9){
          //削除してない生徒で、メールアドレスがない場合は通知
          //@$this->remind("事務管理システム:no=".$item['student_no']."メールアドレス設定なし", 'error', $this->logic_name);
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
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $parent = StudentParent::where('user_id', $parent_user->id)->first();
        //認証情報存在：既存更新
       if(!isset($parent)){
         @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."認証あり / 保護者情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
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
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
         //認証情報存在：既存更新
        $student = Student::where('user_id', $user->id)->first();
        if(!isset($student)){
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."認証あり / 生徒情報なしエラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
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

      //@$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録！:email=".$user->email." / name=".$user->name, 'info', $this->logic_name);
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
      //TODO :以下の属性は申し込み時点でとっていない
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
        @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $student = $student->student;

      $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
      if(!isset($teacher)){
        @$this->remind("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $teacher = $teacher->teacher;

      $lecture = Lecture::where('lecture_id_org',$item['lecture_id'])->first();
      $lecture_id = 0;
      if(isset($lecture)) $lecture_id = $lecture->id;

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
        'lecture_id' => $lecture_id,
        'create_user_id' => 1
      ]);
      return true;
    }
    /**
     * 担当生徒+スケジュール登録
     * @param array $item
     * @return boolean
     */
    private function store_repeat_schedule($item){
      //トレース用
      $message = '';
      foreach($item as $key => $val){
        $message .= $key.'='.$val.'/';
      }

      if(empty($item['kind'])) return false;
      if(!($item['kind']=="m" || $item['kind']=="w")) return false;
      if(empty($item['starttime'])) return false;
      if(empty($item['endtime'])) return false;

      $student = null;
      $teacher = null;
      $manager = null;
      $item['teacher_no'] = $this->get_id_value('teacher', $item);
      if($item['teacher_no']>0){
        $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
        if(!isset($teacher)){
          @$this->remind("事務管理システム:teacher_no=".$item['teacher_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $teacher = $teacher->teacher;
      }
      $item['student_no'] = $this->get_id_value('student', $item);
      $item['student_no'] = intval($item['student_no']);
      if($item['student_no']>0){
        $student = User::tag('student_no', $item['student_no'])->first();
        if(!isset($student)){
          @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $student = $student->student;
      }

      //可能性があるケース
      //講師のみ指定、生徒＋講師の指定、事務のみ指定
      $_data_type = 'student_teacher';
      if($item['student_no']==0 && $item['teacher_no'] > 0){
        $_data_type = 'teacher';
      }
      else if($item['student_no']==0 && $item['teacher_no']==0){
        $_data_type = 'manager';
      }
      $lecture = Lecture::where('lecture_id_org',$item['lecture_id'])->first();
      $lecture_id = 0;
      if(isset($lecture)) $lecture_id = $lecture->id;

      //スケジュール登録方式
      $item["schedule_method"] = "week";
      if($item["kind"]=="m") $item["schedule_method"] = "month";

      $week = ["SU" => "sun", "MO" => "mon", "TU" => "tue", "WE" => "wed", "TH" => "thi", "FR" => "fri", "SA" => "sat"];
      $item["lesson_week"] = "";
      $item["lesson_week_count"] = 0;
      //週N曜日
      $item["dayofmonth"] = trim($item["dayofmonth"]);
      if(!empty($item["dayofmonth"])){
        if(strlen($item["dayofmonth"])==3){
          $item["lesson_week_count"] = substr($item["dayofmonth"], 0, 1);
          $item["dayofweek"] = substr($item["dayofmonth"], 1, 2);
        }
        else {
          //想定外の形式
          @$this->remind("事務管理システム:dayofmonth=".$item['dayofmonth']."の形式が想定外:\n".$message, 'error', $this->logic_name);
        }
      }
      //繰り返し曜日
      if(isset($week[$item["dayofweek"]])) $item["lesson_week"] = $week[$item["dayofweek"]];

      $_attr = $this->get_save_general_attribute('place', $item['place_id'],'');
      $place = $_attr->attribute_value;

      $_attr = $this->get_save_general_attribute('work', $item['work_id'],'');
      $work = $_attr->attribute_value;
      $setting_data = [
        'schedule_method' => $item["schedule_method"],
        'lesson_week_count' => $item["lesson_week_count"],
        'lesson_week' => $item["lesson_week"],
        'from_time_slot' => $item["starttime"],
        'to_time_slot' => $item["endtime"],
        'enable_start_date' => $item["startdate"],
        'enable_end_date' => $item["enddate"],
        'remark' => $item["comment"],
        'lecture_id' => $lecture_id,
        'place' => $place,
        'work' => $work,
        'setting_id_org' => $item["id"],
        'create_user_id' => 1
      ];

      if($_data_type == 'student_teacher'){
        //生徒＋講師指定がある場合
        $charge_student = ChargeStudent::where('student_id', $student->id)
        ->where('teacher_id', $teacher->id)
        ->where('lesson_week', $item["lesson_week"])
        ->where('from_time_slot', $item["starttime"])
        ->where('to_time_slot', $item["endtime"])
        ->where('schedule_method', $item["schedule_method"])
        ->where('lesson_week_count', $item["lesson_week_count"])
        ->first();
        if(!isset($charge_student)){
          //存在しない場合、保存
          ChargeStudent::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'schedule_method' => $item["schedule_method"],
            'lesson_week_count' => $item["lesson_week_count"],
            'lesson_week' => $item["lesson_week"],
            'from_time_slot' => $item["starttime"],
            'to_time_slot' => $item["endtime"],
            'enable_start_date' => $item["startdate"],
            'enable_end_date' => $item["enddate"],
            'lecture_id' => $lecture_id,
            'create_user_id' => 1
          ]);
        }
        else {
          $charge_student->update([
            'enable_start_date' => $item["startdate"],
            'enable_end_date' => $item["enddate"],
            'lecture_id' => $lecture_id,
          ]);
        }
        $setting_data['user_id'] = $teacher->user_id;
        $setting = UserCalendarSetting::add($setting_data);
        $setting->memberAdd($student->user_id, 1, $item["comment"]);
      }
      else if($_data_type=="teacher"){
        $setting_data['user_id'] = $teacher->user_id;
        $setting = UserCalendarSetting::add($setting_data);
      }
      else if($_data_type=="manager"){
        $manager = User::tag('manager_no', $item['user_id'])->first();
        if(!isset($manager)){
          @$this->remind("事務管理システム:manager_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $manager = $manager->manager;
        $setting_data['user_id'] = $manager->user_id;
        $setting = UserCalendarSetting::add($setting_data);
      }

      return true;
    }
    /**
     * 出席データがある→授業予定を出席に更新
     * @param array $item
     * @return boolean
     */
    private function store_attend($item){
      if(empty($item['schedule_id']) || !is_numeric($item['schedule_id'])) return false;

      $calendar = UserCalendar::where('schedule_id', $item['schedule_id'])->first();
      $status = $calendar->status;
      switch(substr($item['attend'],0, 1)){
        case 'a':
          $status = 'rest';
          if($item['attend']=='a2'){
            //TODO:欠席=a2で判断できないので、振替先があるかチェック
            $exchanged_calendar = UserCalendar::where('exchanged_calendar_id', $calendar->id)->first();
            if(!isset($exchanged_calendar)){
              //振替先がないa2=absence
              $status = 'absence';
            }
          }
          break;
        case 'f':
        case 'c':
          //TODO:出席=f / (振替の出席=cは不要なはず)
          $status = 'presence';
          break;
      }

      if($status === $calendar->status){
        //状態変化があれば更新する
        $calendar->update([
          'status' => $status,
        ]);
        //TODO : updateuser(おそらく講師限定）のUserCalendarMemberのstatusを更新すべき
        $members = $calendar->members;
        foreach($memebers as $member){
          $user = $member->$user->details();
          //TODO : 正しくは、updateuser=teacher_noのUserTagを持つかどうか
          if($user->role==='teacher'){
            $member->update(['status' => $status]);
          }
        }
      }
      return true;
    }
    /**
     * カレンダー登録
     * ※※※この処理は使わなくなった
     * @param array $item
     * @return boolean
     */
    private function store_calendar($item){
      $student = User::tag('student_no', $item['student_no'])->first();
      if(!isset($student)){
        @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $student = $student->student;

      $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
      if(!isset($teacher)){
        @$this->remind("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません", 'error', $this->logic_name);
        return false;
      }
      $teacher = $teacher->teacher;

      $remark = $item['comment'];
      $lecture = Lecture::where('lesson',$item['lesson'])
        ->where('course',$item['course'])
        ->where('subject',$item['subject'])
        ->first();
      $lecture_id = 0;
      if(isset($lecture)) $lecture_id = $lecture->id;

      $remark.='[lesson='.$item['lesson'].']';
      $remark.='[course='.$item['course'].']';
      $remark.='[subject='.$item['subject'].']';
      $exchanged_calendar_id = 0;
      //status=1.仮付きの場合：new / 2.仮なし:fix / 3.休み1 or 休み2:rest / 4.出席 : presence 5.欠席:absence
      $status= 'fix';
      if(is_numeric($item['kari_flag']) && $item['kari_flag']=='1'){
        $status= 'new';
      }
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
          'lecture_id' => $lecture_id,
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
          'lecture_id' => $lecture_id,
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
      return true;
    }
    /**
     * カレンダー登録
     * @param array $item
     * @return boolean
     */
    private function store_schedule($item){
      //トレース用
      $message = '';
      foreach($item as $key => $val){
        $message .= $key.'='.$val.'/';
      }

      $item['teacher_no'] = $this->get_id_value('teacher', $item);
      $item['student_no'] = $this->get_id_value('student', $item);
      //TODO : student_no (id?)は数値
      $item['student_no'] = intval($item['student_no']);
      $user_id = 0;
      $student = null;
      if($item['student_no'] > 0){
        $student = User::tag('student_no', $item['student_no'])->first();
        if(!isset($student)){
          @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません\n".$message, 'error', $this->logic_name);
          return false;
        }
        $student = $student->student;
      }
      $teacher = null;
      if($item['teacher_no']>0){
        $teacher = User::tag('teacher_no', $item['teacher_no'])->first();
        if(!isset($teacher)){
          @$this->remind("事務管理システム:teacher_id=".$item['teacher_no']."は、学習管理システムに登録されていません\n".$message, 'error', $this->logic_name);
          return false;
        }
        $teacher = $teacher->teacher;
        $user_id = $teacher->user_id;
      }

      //可能性があるケース
      //講師のみ指定、生徒＋講師の指定、事務のみ指定
      $_data_type = 'student_teacher';
      if($item['student_no']==0 && $item['teacher_no'] > 0){
        $_data_type = 'teacher';
      }
      else if($item['student_no']==0 && $item['teacher_no']==0){
        $_data_type = 'manager';
        $manager = User::tag('manager_no', $item['user_id'])->first();
        if(!isset($manager)){
          @$this->remind("事務管理システム:manager_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $manager = $manager->manager;
        $user_id = $manager->user_id;
      }

      $lecture = Lecture::where('id',$item['lecture_id'])
        ->first();
      $lecture_id = 0;
      if(isset($lecture)) $lecture_id = $lecture->id;

      $remark = $item['comment'];
      $remark.='[free='.$item['free'].']';
      $remark.='[repeattimes='.$item['repeattimes'].']';
      $exchanged_calendar_id = 0;
      $status= 'fix';
      if(isset($student) && isset($teacher)){
         if(strtotime($item['ymd']) > strtotime(date('Y-m-d'))){
           $status = 'fix';
         }
      }
      else {
        //講師・生徒いずれかセットされていない
        $status = 'new';
      }
      if(is_numeric($item['temporary'])){
        switch(intval($item['temporary'])){
          case 101:
          case 111:
            //生徒確認済み
            //TODO : 101は生徒確認・講師未確認
            $status="fix";
            break;
          case 11:
            //講師確認済み
            $status="confirm";
          case 1:
            $status="new";
            break;
        }
      }
      if(!empty(trim($item['cancel']))){
        //TODO :以下の項目をどうにかしたい
        //c = すべからずcancel
        //それ以外、何等か休暇(a :休暇、a1:休み1、休み2）。
        $_attr = $this->get_save_general_attribute('absence_type', '', $item['cancel']);
        $yasumi = $_attr->attribute_value;
        $remark.='[cancel='.$item['cancel'].']';
        if($item['cancel']==='c')  $status = 'cancel';
        else $status = 'rest';
      }
      if(isset($item['altsched_id']) && $item['altsched_id']>0){
        $exchanged_calendar = UserCalendar::where('schedule_id',$item['altsched_id'])->first();
        $exchanged_calendar_id = $exchanged_calendar->id;
      }
      $_attr = $this->get_save_general_attribute('place', $item['place_id'],'');
      $place = $_attr->attribute_value;

      $_attr = $this->get_save_general_attribute('work', $item['work_id'],'');
      $work = $_attr->attribute_value;

      $calendar_id = 0;
      $items = UserCalendar::where('schedule_id',$item['id'])->first();
      $update_form = [
        'start_time' => $item['ymd'].' '.$item['starttime'],
        'end_time' => $item['ymd'].' '.$item['endtime'],
        'user_id' => $user_id,
        'lecture_id' => $lecture_id,
        'exchanged_calendar_id' => $exchanged_calendar_id,
        'remark' => $remark,
        'status' => $status,
        'place' => $place,
        'work' => $work,
      ];
      if(isset($items)){
        //すでに存在する場合は更新する
        $items->update($update_form);
        $calendar_id = $items->id;
      }
      else {
        $update_form['schedule_id'] = $item['id'];
        $update_form['create_user_id'] = 1;
        $items = UserCalendar::create($update_form);
        $calendar_id = $items->id;
      }
      //いったんすべて参加者を削除
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
      //TODO : 休みに関し、生徒起因か、講師起因かがわからない
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
      //講師 or 事務をカレンダーに追加
      UserCalendarMember::create([
        'calendar_id' => $calendar_id,
        'status' => $teacher_status,
        'user_id' => $user_id,
        'create_user_id' => 1
      ]);
      //生徒をカレンダーに追加
      if(isset($student)){
        UserCalendarMember::create([
          'calendar_id' => $calendar_id,
          'user_id' => $student->user_id,
          'status' => $student_status,
          'create_user_id' => 1
        ]);
      }
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
    private function remind($message, $type, $title){
      @$this->send_slack($message, $type, $title);
    }
    private function get_id_value($prefix, $item, $is_null_value=0){
      $key = $prefix.'_no';
      $value = $is_null_value;
      if(empty($item[$key]) || intval($item[$key])==0){
        $key = $prefix.'_id';
        if(empty($item[$key]) || intval($item[$key])==0){
          //取得できなかった場合
          return $value;
        }
        //_idの方を取得
        $value = $item[$key];
      }
      else {
        //_noの方を取得
        $value = $item[$key];
      }
      if(empty($value)) return $is_null_value;
      return $value;
    }
    private function concealment(){
      $ret = [];
      $env = config('app.env');
      if($env!=="product"){
        //本番でない場合、保護者あてのメールを隠す
        $query = <<<EOT
          update users set email=concat('yasui.hideo+p',id,'@gmail.com')
           where id in (select user_id from student_parents)
EOT;
        $ret[] = DB::update($query, []);
        @$this->remind("契約者のメールアドレスを秘匿(".$env.")", 'info', $this->logic_name);
      }
      if($env!=="product" && $env !== "staging"){
        //staging or productでない場合、講師あて、事務あてのメールを隠す
        $query = <<<EOT
          update users set email=concat('yasui.hideo+t',id,'@gmail.com')
           where id in (select user_id from teachers)
EOT;
        $ret[] = DB::update($query, []);
        $query = <<<EOT
          update users set email=concat('yasui.hideo+m',id,'@gmail.com')
           where id in (select user_id from managers)
EOT;
        $ret[] = DB::update($query, []);
        @$this->remind("講師・事務のメールアドレスを秘匿(".$env.")", 'info', $this->logic_name);
      }
      return $ret;
    }
}
