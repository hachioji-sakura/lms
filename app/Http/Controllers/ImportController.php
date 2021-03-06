<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Models\GeneralAttribute;
use App\Models\PlaceFloor;
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
use App\Models\ChargeStudentTag;

use App\Models\UserCalendar;
use App\Models\UserCalendarTag;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;
use App\Models\UserCalendarTagSetting;

use App\Models\StudentGroup;

use Illuminate\Http\Request;

use DB;
class ImportController extends UserController
{
    //事務管理システム側の情報
    //API URL: domain+endpoint+.php?query_string
    public $logic_name = '事務管理システム-データ取り込み';
    public $api_domain = '/sakura-api';
    public $api_endpoint = [
      'works' => 'api_get_work_explanation',
      'courses' =>  'api_get_course',
      'subjects' =>  'api_get_subject',
      'lessons' =>  'api_get_lesson',
      'lectures' =>  'api_get_lecture',
      'students' =>  'api_get_student',
      'teachers' =>  'api_get_teacher',
      'managers' =>  'api_get_staff',
      'textbooks' =>  'api_get_material',
      'charge_students' =>  'api_get_teacherstudent',
      'repeat_schedules' =>  'api_get_repeat_schedule',
      'schedules' => 'api_get_onetime_schedule',
      'season_schedules' => '',
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
      $this->token = $request->header('api-token');
      if($object==='concealment'){
        $res = $this->concealment();
        return $res;
      }
      if($object==='set_password'){
        $password = Hash::make('sakusaku');
        $users = User::where('id','>',1)->update(['password'=>$password]);
        $password = Hash::make('password');
        $users = User::where('id',1)->update(['password'=>$password]);
        return "set_password";
      }
      if(!array_key_exists($object, $this->api_endpoint)){
        return $this->bad_request();
      }
      $url = config('app.management_url').$this->api_domain.'/'.$this->api_endpoint[$object].'.php';
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
      $this->token = $request->header('api-token');

      set_time_limit(1200);
      if($object==='concealment'){
        $res = $this->concealment();
        return $res;
      }
      else if($object==='test'){
        $res = $this->test();
        return $res;
      }
      else if($object=='all'){
        $objects = [
          'works',
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
        if($object == 'season_schedules'){
          $this->logic_name = "季節講習カレンダーデータ取り込み";
          $res = $this->season_schedules_import();
          return $res;
        }
        $url = config('app.management_url').$this->api_domain.'/'.$this->api_endpoint[$object].'.php';
        @$this->remind('import call_api['.$url.']', 'info', $this->logic_name);
        $res = $this->call_api($request, $url);
        if(!$this->is_success_response($res)){
          return $this->error_response('api error', $url);
        }
        $items = $res['data'];
        switch($object){
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
          case 'repeat_schedules':
            $this->logic_name = "繰り返しスケジュール取り込み";
            $res = $this->repeat_schedules_import($items);
            break;
          case 'textbooks':
            $this->logic_name = "参考書データ取り込み";
            $res = $this->textbooks_import($items);
            break;
          case 'schedules':
            $this->logic_name = "カレンダーデータ取り込み";
            $res = $this->schedules_import($items);
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
    private function season_schedules_import(){
      set_time_limit(3600);
      $req = new Request;

      //季節講習の取り込みのため一度、すべて削除
      $season_calendars = UserCalendar::whereIn('work', [10, 11])->get();
      $ids = [];
      foreach($season_calendars as $season_calendar){
        $ids[] = $season_calendar->id;
      }
      if(count($ids) > 0){
        UserCalendarMember::whereIn('calendar_id', $ids)->delete();
        UserCalendarTag::whereIn('calendar_id', $ids)->delete();
        UserCalendar::whereIn('id', $ids)->delete();
      }
      //季節講習の取り込み(work_id=10 / 11のものすべて取り込む）
      $url = config('app.url').'/import/schedules?work_id=10';
      $res = $this->call_api($req, $url, 'POST');
      if($res["status"] == 200){
        $work_10_count = $res["data"];
        $url = config('app.url').'/import/schedules?work_id=11';
        $res = $this->call_api($req, $url, 'POST');
        if($res["status"] == 200){
          $res["work_11_count"] = $res["data"];
          $res["work_10_count"] = $work_10_count;
        }
      }
      return $res;
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function general_attributes_import($items, $key_name, $id_column, $idname_column){
        return $this->transaction(null, function() use ($items, $key_name, $id_column, $idname_column){
          $c=0;
          foreach($items as $item){
            if($this->store_general_attribute($key_name, $item[$id_column], $item[$idname_column])) $c++;
          }
          return $this->api_response(200, '', '', $key_name.'['.$c.']');
        }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function students_import($items){
      return $this->transaction(null, function() use ($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_student($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
    }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function teachers_import($items){
      return $this->transaction(null, function() use ($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_teacher($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function managers_import($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_manager($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
        return $this->transaction(null, function() use ($items){
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function repeat_schedules_import($items){
      return $this->transaction(null, function() use ($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_repeat_schedule($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function textbooks_import($items){
      return $this->transaction(null, function() use ($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_textbook($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
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
        return $this->api_response(200, '', '', 'count['.$c.']');
        return $this->transaction(null, function() use ($items){
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function lectures_import($items){
      return $this->transaction(null, function() use ($items){
        $c = 0;
        foreach($items as $item){
          if($this->store_lecture($item)) $c++;
        }
        return $this->api_response(200, '', '', 'count['.$c.']');
      }, 'インポート', __FILE__, __FUNCTION__, __LINE__ );
    }
    /**
     * 事務情報登録
     * @param array $item
     * @return boolean
     */
    private function store_manager($item){
      $item['staff_no'] = $this->get_id_value('staff', $item);
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
        $item['kana_last'] = mb_convert_kana($kanas[0], "KVC");
        $item['kana_first'] = mb_convert_kana($kanas[1], "KVC");
      }

      $item['name_last'] = '';
      $item['name_first'] = '';
      if(!empty($item['staff_name'])){
          $names = explode(' ', $item['staff_name'].' ');
          $item['name_last'] = $names[0];
          $item['name_first'] = $names[1];
      }
      $status = 'regular';
      if(isset($item['unsubscribe']) && is_numeric($item['unsubscribe']) && intval($item['unsubscribe']) > 0){
        $status = 'unsubscribe';
      }

      //注意：APIは、acountとして送信される　→ 正しくは、account
      $manager = Manager::hasTag('manager_no', $item['staff_no'])->first();
      $bank_account_type = [1=>'normal', 2=>'current', 3=>'savings'];
      $_bank_account_type = "";
      if(isset($bank_account_type[intval($item['bank_acount_type'])])){
        $_bank_account_type = $bank_account_type[intval($item['bank_acount_type'])];
      }
      $teacher = null;
      $user_id = -1;
      if(!isset($manager)){
        //登録されていないが、講師側に同一氏名のユーザーが存在する場合
        $teacher = Teacher::where('name_last', $item['name_last'])->where('name_first', $item['name_first'])->first();
        if(isset($teacher)){
          //兼務
          $user_id = $teacher->user_id;
        }
      }
      else {
        $user_id = $manager->user_id;
      }
      if($user_id < 1){
        //認証情報登録
        $res = $this->user_create([
          'name' => $item['staff_no'],
          'password' => $item['password'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)) $user_id = $res['data']->id;
      }
      if(!isset($manager)){
        $manager = Manager::where('name_last', $item['name_last'])->where('name_first', $item['name_first'])->first();
      }
      if(!isset($manager)){
        if($user_id > 0 ){
          //講師情報登録
          $manager = Manager::create([
            'status' => $status,
            'name_last' => $item['name_last'],
            'name_first' => $item['name_first'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'user_id' => $user_id,
            'bank_no' => $item['bank_no'],
            'bank_branch_no' => $item['bank_branch_no'],
            'bank_account_type' => $_bank_account_type,
            'bank_account_no' => $item['bank_acount_no'],
            'bank_account_name' => $item['bank_acount_name'],
            'create_user_id' => 1
          ]);
        }
        else {
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['staff_no']."登録エラー:email=".$user->email." / name=".$user->name, 'error', $this->logic_name);
          return false;
        }
      }
      else {
        $manager->update([
          'status' => $status,
          'name_last' => $item['name_last'],
          'name_first' => $item['name_first'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
          'bank_no' => $item['bank_no'],
          'bank_branch_no' => $item['bank_branch_no'],
          'bank_account_type' => $_bank_account_type,
          'bank_account_no' => $item['bank_acount_no'],
          'bank_account_name' => $item['bank_acount_name'],
        ]);
      }
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
          $item['kana_last'] = mb_convert_kana($kanas[0], "KVC");
          $item['kana_first'] = mb_convert_kana($kanas[1], "KVC");
      }

      $item['name_last'] = '';
      $item['name_first'] = '';
      if(!empty($item['teacher_name'])){
          $names = explode(' ', $item['teacher_name'].' ');
          $item['name_last'] = $names[0];
          $item['name_first'] = $names[1];
      }

      $teacher = Teacher::hasTag('teacher_no', $item['teacher_no'])->first();
      $bank_account_type = [1=>'normal', 2=>'current', 3=>'savings'];
      $_bank_account_type = "";
      if(isset($bank_account_type[intval($item['bank_acount_type'])])){
        $_bank_account_type = $bank_account_type[intval($item['bank_acount_type'])];
      }
      $status = 'regular';
      if(isset($item['unsubscribe']) && is_numeric($item['unsubscribe']) && intval($item['unsubscribe']) > 0){
        $status = 'unsubscribe';
      }
      if(!isset($teacher)){
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
          $teacher = $Teacher->create([
            'name_last' => $item['name_last'],
            'name_first' => $item['name_first'],
            'kana_last' => $item['kana_last'],
            'kana_first' => $item['kana_first'],
            'user_id' => $res['data']->id,
            'bank_no' => $item['bank_no'],
            'status' => $status,
            'bank_branch_no' => $item['bank_branch_no'],
            'bank_account_type' => $_bank_account_type,
            'bank_account_no' => $item['bank_acount_no'],
            'bank_account_name' => $item['bank_acount_name'],
            'create_user_id' => 1
          ]);
        }
        else {
          @$this->remind("事務管理システム:email=".$item['email']." / name=".$item['teacher_no']."登録エラー:".$res["message"]."/".$res["description"]."/".$res["status"]);
          return false;
        }
        //講師属性登録
        $this->store_user_tag($teacher->user_id, 'teacher_no', $item['teacher_no'], false);
        if($item['lesson_id']!='0') $this->store_user_tag($teacher->user_id, 'lesson', $item['lesson_id'], false);
        if($item['lesson_id2']!='0') $this->store_user_tag($teacher->user_id, 'lesson', $item['lesson_id2'], false);
      }
      else {
        $teacher->update([
          'name_last' => $item['name_last'],
          'name_first' => $item['name_first'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
          'bank_no' => $item['bank_no'],
          'status' => $status,
          'bank_branch_no' => $item['bank_branch_no'],
          'bank_account_type' => $_bank_account_type,
          'bank_account_no' => $item['bank_acount_no'],
          'bank_account_name' => $item['bank_acount_name'],
        ]);
      }
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
        $item['_birth_day'] = '9999-12-31';
      }

      $item['status'] = 1; //インポートしただけで、アカウント通知が必要な状況
      if(isset($item['del_flag']) && intval($item['del_flag'])===2){
        $item['status'] = 9;
      }
      $status = 'regular';
      if(isset($item['unsubscribe']) && is_numeric($item['unsubscribe']) && intval($item['unsubscribe']) > 0){
        $status = 'unsubscribe';
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
          $item['kana_last'] = mb_convert_kana($kanas[0], "KVC");
          $item['kana_first'] = mb_convert_kana($kanas[1], "KVC");
      }
      //student_noを持った生徒が登録済みかどうか
      //事務員の可能性もある
      $parent_user = User::where('email', $item['email'])->first();
      //認証情報登録(保護者として登録）
      if(!isset($parent_user)){
        $res = $this->user_create([
          'name' => $item['family_name'].'',
          'password' => $item['password'],
          'email' => $item['email'],
          'image_id' => 4,
          'status' => $item['status'],
        ]);
        if($this->is_success_response($res)){
          $parent_user = $res['data'];
        }
      }
      $parent = StudentParent::where('user_id', $parent_user->id)->first();
      if(!isset($parent)){
        //保護者情報登録
        $parent = StudentParent::create([
          'status' => $status,
          'name_last' => $item['family_name'],
          'name_first' => $item['first_name'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
          'user_id' => $parent_user->id,
          'create_user_id' => 1,
        ]);
      }
      $student = Student::hasTag('student_no', $item['student_no'])->first();
      if(!isset($student)){
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
            'status' => $status,
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
        $StudentRelation = new StudentRelation;
        $StudentRelation->create([
          'student_id' => $student->id,
          'student_parent_id' => $parent->id,
          'create_user_id' => 1,
        ]);
      }
      else {
        $student->update([
          'name_last' => $item['family_name'],
          'name_first' => $item['first_name'],
          'kana_last' => $item['kana_last'],
          'kana_first' => $item['kana_first'],
          'birth_day' => $item['_birth_day'],
          'gender' => $item['gender'],
        ]);
        if($status == 'unsubscribe'){
          //削除時のみ更新
          $student->user->update([
            'status' => $item['status'],
          ]);
          $student->update([
            'status' => $status,
          ]);
          foreach($student->relations as $relation){
            if($relation->parent->status == 'unsubscribe') continue;
            $_status = 'unsubscribe';
            foreach($relation->parent->relation() as $relation){
              if($relation->student->status != 'unsubscribe'){
                //有効な生徒が一人以上いる
                $_status = 'regular';
                break;
              }
            }
            if($_status == 'unsubscribe'){
              //有効な生徒が一人もいない
              $relation->parent->update([
                'status' => $_status,
              ]);
            }
          }
        }
      }
      //@$this->remind("事務管理システム:email=".$item['email']." / name=".$item['student_no']."登録！:email=".$user->email." / name=".$user->name, 'info', $this->logic_name);
      //生徒属性登録
      UserTag::where('user_id', $student->user_id)->whereIn('tag_key', ['student_no', 'grade', 'student_type', 'grade_adj'])->delete();
      //生徒種別：ほとんどが3=生徒なので取得不要と思う、2=職員？、1=本部？
      //$this->store_user_tag($user->id, 'student_kind', $item['student_kind']);
      $this->store_user_tag($student->user_id, 'student_no', $item['student_no'], false);
      $grade_tag = $this->store_user_tag($student->user_id, 'grade', $item['grade']);
      //TODO :以下の属性は申し込み時点でとっていない
      if(isset($grade_tag) && isset($grade_tag->tag_value)){
        if(is_numeric($item['jyukensei']) && $item['jyukensei']=='1'){
          $this->store_user_tag($student->user_id, 'student_type', 'juken', false);
          //中学受験タグの設定
          if($grade_tag->tag_value=='e4' || $grade_tag->tag_value=='e5' || $grade_tag->tag_value=='e6'){
            $this->store_user_tag($student->user_id, 'student_type', 'j_juken', false);
          }
        }
      }
      if(is_numeric($item['fee_free']) && $item['fee_free']=='1'){
        $this->store_user_tag($student->user_id, 'student_type', 'fee_free', false);
      }
      if(is_numeric($item['yuge_price']) && $item['yuge_price']=='1'){
        $this->store_user_tag($student->user_id, 'student_type', 'yuge_price', false);
      }
      $this->store_user_tag($student->user_id, 'grade_adj', $item['grade_adj']);
      return true;
    }
    /**
     * 担当生徒登録
     * @param array $item
     * @return boolean
     */
    private function store_charge_student($student_id, $teacher_id, $tags){
      $charge_student = ChargeStudent::where('student_id', $student_id)
      ->where('teacher_id', $teacher_id)
      ->first();

      if(!isset($charge_student)){
        //存在しない場合、保存
        $charge_student = ChargeStudent::create([
          'student_id' => $student_id,
          'teacher_id' => $teacher_id,
          'create_user_id' => 1
        ]);
        $tag_names = ['course_minutes', 'course_type', 'lesson', 'subject_expr'];
        foreach($tag_names as $tag_name){
          if(!empty($tags[$tag_name])){
            $this->store_charge_student_tag($charge_student->id, $tag_name, $tags[$tag_name]);
          }
        }
      }
      return $charge_student;
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
      $tags = [];
      $user_id = 0;

      //授業時間
      $course_minutes = intval(strtotime('2000-01-01 '.$item['endtime']) - strtotime('2000-01-01 '.$item['starttime']))/60;

      $student = null;
      $teacher = null;
      $manager = null;
      $item['teacher_no'] = $this->get_id_value('teacher', $item);
      if($item['teacher_no']>0){
        $teacher = Teacher::hasTag('teacher_no', $item['teacher_no'])->first();
        if(!isset($teacher)){
          @$this->remind("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
      }
      $item['student_no'] = $this->get_id_value('student', $item);
      $item['student_no'] = intval($item['student_no']);
      if($item['student_no']!=0){
        $student = Student::hasTag('student_no', $item['student_no'])->first();
        if(!isset($student)){
          @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
      }

      //可能性があるケース
      //講師のみ指定、生徒＋講師の指定、事務のみ指定
      $_data_type = 'student_teacher';
      if($item['student_no']==0 && $item['teacher_no'] > 0){
        $_data_type = 'teacher';
      }
      else if($item['student_no'] > 0 && $item['teacher_no'] == 0){
        $_data_type = 'student';
      }
      else if($item['student_no']==0 && $item['teacher_no']==0){
        $_data_type = 'manager';
      }
      $lecture = Lecture::where('lecture_id_org',$item['lecture_id'])->first();
      $lecture_id = 0;
      if(isset($lecture)){
        $lecture_id = $lecture->id;
        $replace_course_type = config('replace.course_type');
        $course = intval($lecture->course);
        if(isset($replace_course_type[$course])){
          $tags['course_type'] = $replace_course_type[$course];
        }
        $tags['lesson'] = $lecture->lesson;
      }
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

      $_attr = $this->get_save_general_attribute('work', $item['work_id'],'');
      $work = $_attr->attribute_value;
      $setting_data = [
        'schedule_method' => $item["schedule_method"],
        'lesson_week_count' => $item["lesson_week_count"],
        'lesson_week' => $item["lesson_week"],
        'course_minutes' => $course_minutes,
        'from_time_slot' => $item["starttime"],
        'to_time_slot' => $item["endtime"],
        'enable_start_date' => $this->get_date($item["startdate"]),
        'enable_end_date' => $this->get_date($item["enddate"]),
        'remark' => $item["comment"],
        'lecture_id' => $lecture_id,
        'place_floor_id' => $item['place_id'],
        'work' => $work,
        'status' => 'fix',
        'create_user_id' => 1
      ];

      $member = UserCalendarMemberSetting::where('setting_id_org', $item['id'])->first();
      if(isset($member)) $setting = $member->setting;

      $user_id = 0;
      if($_data_type == 'student_teacher'){
        //生徒＋講師指定がある場合
        if(!isset($teacher)){
          @$this->remind("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $teacher->user_id;
        if(!isset($setting) && ($work==7 || $work==8) && !empty($user_id)){
          //$user_idが設定されるケースは、事務or講師
          $__setting = UserCalendarSetting::where('user_id', $user_id)
            ->where('schedule_method', $item['schedule_method'])
            ->where('lesson_week_count', $item['lesson_week_count'])
            ->where('lesson_week', $item['lesson_week'])
            ->where('from_time_slot', $item['starttime'])
            ->where('to_time_slot', $item['endtime'])
            ->where('work' , $work)
            ->where('place_floor_id', $item['place_id'])
            ->first();
          if(isset($__setting)){
            //おそらく同一のグループレッスンと思われる予定が見つかった
            $setting = $__setting;

          }
        }
      }
      else if($_data_type=="student"){
        if(!isset($student)){
          @$this->remind("事務管理システム:id=".$item['id']."/student_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $student->user_id;
      }
      else if($_data_type=="teacher"){
        if(!isset($teacher)){
          @$this->remind("事務管理システム:id=".$item['id']."/teacher_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $teacher->user_id;
      }
      else if($_data_type=="manager"){
        $manager = Manager::hasTag('manager_no', $item['user_id'])->first();
        if(!isset($manager)){
          @$this->remind("事務管理システム:id=".$item['id']."/manager_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $manager->user_id;
      }
      if(isset($setting)){
        //既存更新
        $setting->update($setting_data);
      }
      else {
        //新規登録
        $setting_data['user_id'] = $user_id;
        $setting = UserCalendarSetting::create($setting_data);
      }
      $__member = UserCalendarMemberSetting::where('user_calendar_setting_id', $setting->id)
                  ->where('user_id', $user_id)
                  ->first();
      if(!isset($__member)){
        $member = UserCalendarMemberSetting::create([
            'user_calendar_setting_id' => $setting->id,
            'user_id' => $user_id,
            'remark' => '',
            'create_user_id' => 1,
            'setting_id_org' => $item['id'],
        ]);
      }
      if(isset($student)){
        $__member = UserCalendarMemberSetting::where('user_calendar_setting_id', $setting->id)
                  ->where('user_id', $student->user_id)
                  ->first();
        if(!isset($__member)){
          $__member = UserCalendarMemberSetting::create([
              'user_calendar_setting_id' => $setting->id,
              'user_id' => $student->user_id,
              'remark' => '',
              'create_user_id' => 1,
              'setting_id_org' => $item['id'],
          ]);
        }
      }
      if($work!=9){
        //事務システムから取得した科目
        if(!empty(trim($item['subject_expr']))){
          $tags['subject_expr'] = $item['subject_expr'];
        }
        $tag_names = ['course_type', 'lesson', 'subject_expr'];
        foreach($tag_names as $tag_name){
          if(!empty($tags[$tag_name])){
            if(isset($setting)){
               $this->store_calendar_setting_tag($setting->id, $tag_name, $tags[$tag_name]);
             }
            if(!isset($student->user_id)){
              continue;
            }
            $this->store_user_tag($student->user_id, $tag_name, $tags[$tag_name], false);
          }
        }
        if(isset($student) && isset($teacher)){
          $this->store_charge_student($student->id,$teacher->id,$tags);
        }
      }
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
      if(empty($item['starttime'])) return false;
      if(empty($item['endtime'])) return false;
      $tags = [];
      $start_time = $item['ymd'].' '.$item['starttime'];
      $end_time = $item['ymd'].' '.$item['endtime'];
      //授業時間
      $course_minutes = intval(strtotime('2000-01-01 '.$item['endtime']) - strtotime('2000-01-01 '.$item['starttime']))/60;

      $item['teacher_no'] = $this->get_id_value('teacher', $item);
      $item['student_no'] = $this->get_id_value('student', $item);
      //TODO : student_no (id?)は数値
      $item['student_no'] = intval($item['student_no']);
      $user_id = 0;
      $student = null;
      if($item['student_no']!=0){
        $student = Student::hasTag('student_no', $item['student_no'])->first();
        if(!isset($student) || empty($student->user_id)){
          @$this->remind("事務管理システム:student_no=".$item['student_no']."は、学習管理システムに登録されていません\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $student->user_id;
      }
      $teacher = null;
      if($item['teacher_no']>0){
        $teacher = Teacher::hasTag('teacher_no', $item['teacher_no'])->first();
        if(!isset($teacher)){
          @$this->remind("事務管理システム:teacher_no=".$item['teacher_no']."は、学習管理システムに登録されていません\n".$message, 'error', $this->logic_name);
          return false;
        }
        $user_id = $teacher->user_id;
      }
      //work
      $_attr = $this->get_save_general_attribute('work', $item['work_id'],'');
      $work = $_attr->attribute_value;

      //ステータス初期設定
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

      //可能性があるケース
      //講師のみ指定、生徒＋講師の指定、事務のみ指定
      $_data_type = 'student_teacher';
      if($item['student_no']==0 && $item['teacher_no'] > 0){
        $_data_type = 'teacher';
      }
      else if($item['student_no']>0 && $item['teacher_no'] == 0){
        $_data_type = 'student';
      }
      else if($item['student_no']==0 && $item['teacher_no']==0){
        $_data_type = 'manager';
        $manager = Manager::hasTag('manager_no', $item['user_id'])->first();
        if(!isset($manager)){
          $manager = Teacher::hasTag('manager_no', $item['user_id'])->first();
          if(!isset($manager)){
            @$this->remind("事務管理システム:manager_no=".$item['user_id']."は、学習管理システムに登録されていません:\n".$message, 'error', $this->logic_name);
            return false;
          }
        }
        $user_id = $manager->user_id;
        $status = "fix";
      }
      //レクチャ取得
      $lecture = Lecture::where('lecture_id_org',$item['lecture_id'])
        ->first();
      $lecture_id = 0;
      if(isset($lecture)) {
        $lecture_id = $lecture->id;
      }

      //その他の項目は、remarkにでも入れておく
      $remark = $item['comment'];

      $exchanged_calendar_id = 0;

      if(is_numeric($item['temporary'])){
        switch(intval($item['temporary'])){
          case 101:
          case 111:
            //生徒確認済み
            // 101は生徒確認・講師未確認
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
      if($status=='new' && ($work==5 || $work==11)){
        //演習の場合 fix
        $status = 'fix';
      }

      if(!empty(trim($item['cancel']))){
        //TODO :以下の項目をどうにかしたい
        //c = すべからずcancel
        //それ以外、何等か休暇(a :休暇、a1:休み1、休み2, c:別の日時に変更された）。
        //$_attr = $this->get_save_general_attribute('absence_type', '', $item['cancel']);
        //$yasumi = $_attr->attribute_value;

        //TODO 2019.10.5 問題点
        //a1と来た場合、lecture_cancelとなるが、実際、忖度で休み１にした場合、status=rest / a1とすべきだが、
        //inputが明確に取れないので、a1はすべてlecture_cancelにするしかない
        $remark.='[cancel='.$item['cancel'].']';
        if($item['cancel']==='c')  $status = 'cancel';
        else if($item['cancel']==='a1')  $status = 'lecture_cancel';
        else $status = 'rest';
      }
      if(!empty(trim($item['confirm']))){
        //TODO :以下の項目をどうにかしたい
        //出席もしくは出勤確認(confirm):“f”、休み:”a”、休み1:”a1”、休み2:”a2”、振替出席(change):”c”
        $remark.='[confirm='.$item['confirm'].']';
        if($item['confirm']==='f')  $status = 'presence';
        else if($item['confirm']==='c')  $status = 'presence';
        else if($item['confirm']==='a2')  $status = 'absence';
      }

      //振替
      if(isset($item['altsched_id']) && $item['altsched_id']!=0){
        //事務システムの振替ID＝メンバーのIDを指している（メンバーとカレンダーが１：１だから）
        $exchanged_calendar_member = UserCalendarMember::where('schedule_id',$item['altsched_id'])->first();
        if(isset($exchanged_calendar_member)) {
          $exchanged_calendar_id = $exchanged_calendar_member->calendar_id;
        }
        else {
          //TODO 振替元がわからないが振替の予定
          $exchanged_calendar_id = -9999;
        }
      }

      //場所
      $floor = PlaceFloor::where('id', $item['place_id'])->first();
      $sheat_id = 0;
      if(isset($floor)){
        $sheat = $floor->get_free_seat($start_time, $end_time);
        if(isset($sheat)){
          $sheat_id = $sheat->id;
        }
      }

      $calendar_id = 0;
      $_member = UserCalendarMember::where('schedule_id',$item['id'])->first();
      $items = null;
      if(isset($_member)) $items = $_member->calendar;
      if(!isset($items) && !empty($user_id) && ($work==7 || $work==8)){
        $__items = UserCalendar::where('user_id', $user_id)
          ->where('start_time', $start_time)
          ->where('end_time' , $end_time)
          ->where('work' , $work)
          ->where('lecture_id' , $lecture_id)
          ->where('place_floor_id', $item['place_id'])
          ->first();
        if(isset($__items)){
          //おそらく同一のグループレッスンと思われる予定が見つかった
          $items = $__items;
        }
      }
      $update_form = [
        'start_time' => $start_time,
        'end_time' => $end_time,
        'user_id' => $user_id,
        'lecture_id' => $lecture_id,
        'course_minutes' => $course_minutes,
        'exchanged_calendar_id' => $exchanged_calendar_id,
        'remark' => $remark,
        'status' => $status,
        'place_floor_id' => $item['place_id'],
        'work' => $work,
      ];

      if(isset($items)){
        if(intval($item['delflag'])==1){
          UserCalendarMember::where('calendar_id', $items->id)->delete();
          UserCalendarTag::where('calendar_id', $items->id)->delete();
          UserCalendar::where('id', $items->id)->delete();
          return true;
        }
        else {
          //すでに存在する場合は更新する
          $items->update($update_form);
          $calendar_id = $items->id;
        }
      }
      else {
        $update_form['create_user_id'] = 1;
        $items = UserCalendar::create($update_form);
        $calendar_id = $items->id;
      }
      //いったんすべて参加者を削除
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
      $student_status = $status;
      if(empty(trim($item['altlimitdate']))) $item['altlimitdate'] = null;
      //生徒をカレンダーに追加
      if(isset($student)){
        $_member = UserCalendarMember::where('calendar_id' , $calendar_id)
          ->where('user_id' , $student->user_id)
          ->first();
        if(!isset($_member)){
          //生徒を追加
          UserCalendarMember::create([
            'calendar_id' => $calendar_id,
            'user_id' => $student->user_id,
            'status' => $student_status,
            'remark' => $item['comment'],
            'exchange_limit_date' => $item['altlimitdate'],
            'rest_type' => $item['cancel'],
            'rest_result' => $item['cancel_reason'],
            'schedule_id' => $item['id'],
            'place_floor_sheat_id' => $sheat_id,
            'create_user_id' => 1
          ]);
        }
        else {
          $_member->update([
            'status' => $student_status,
            'remark' => $item['comment'],
            'rest_result' => $item['cancel_reason'],
          ]);
        }
      }
      //講師 or 事務をカレンダーに追加
      $teacher_status = $status;
      $_member = UserCalendarMember::where('calendar_id' , $calendar_id)
        ->where('user_id' , $user_id)
        ->first();
      if(!isset($_member)){
        //講師を追加
        UserCalendarMember::create([
          'calendar_id' => $calendar_id,
          'status' => $teacher_status,
          'rest_type' => $item['cancel'],
          'exchange_limit_date' => $item['altlimitdate'],
          'remark' => $item['comment'],
          'rest_result' => '',
          'schedule_id' => $item['id'],
          'place_floor_sheat_id' => $sheat_id,
          'user_id' => $user_id,
          'create_user_id' => 1
        ]);
      }
      else {
        $_member->update(['status' => $student_status]);
      }
      //事務システムから取得した科目
      if(!empty(trim($item['subject_expr']))){
        $tags['subject_expr'] = trim($item['subject_expr']);
      }
      //TODO lectureはほぼ使わない
      if(isset($lecture)) {
        $lecture_id = $lecture->id;
        $replace_course_type = config('replace.course_type');
        $course = intval($lecture->course);
        if(isset($replace_course_type[$course])){
          $tags['course_type'] = $replace_course_type[$course];
        }
        $tags['lesson'] = $lecture->lesson;
      }

      $tag_names = ['course_type', 'lesson', 'subject_expr'];
      foreach($tag_names as $tag_name){
        if(!empty($tags[$tag_name])){
          $this->store_calendar_tag($calendar_id, $tag_name, $tags[$tag_name]);
        }
      }
      if(isset($student) && isset($teacher)){
         $this->store_charge_student($student->id, $teacher->id, $tags);
      }

      $setting_id = 0;
      $calendar = UserCalendar::where('id', $calendar_id)->first();
      //カレンダー設定によるものか探す
      $w = date('w', strtotime($item['ymd']));
      $start_date = $item['ymd'];
      $week = ["sun", "mon", "tue", "wed", "thi", "fri", "sat"];
      $lesson_week = $week[$w];
      //曜日・時間・作業内容・場所・ユーザーが一致する
      $settings = UserCalendarSetting::where('user_id', $user_id)
        ->where('from_time_slot', $item['starttime'])
        ->where('to_time_slot', $item['endtime'])
        ->where('work', $work)
        ->where('place_floor_id', $item['place_id'])
        ->where('lesson_week', $lesson_week)
        ->enable()
        ->get();
      foreach($settings as $setting){
        $is_member = true;
        foreach($calendar->members as $member){
          if($setting->is_member($member->user_id)==false){
            //設定に参加者が含まれていない
            $is_member = false;
            break;
          }
        }
        //参加者が同じ場合、この設定が通常授業設定確定
        if($is_member===true) {
          $setting_id = $setting->id;
        }
      }
      \Log::warning("-------------setting_id=".$setting_id.'-------------');
      if($setting_id > 0){
        $calendar->update(['user_calendar_setting_id' => $setting_id]);
      }
      $teaching_type = $calendar->get_teaching_type();
      $calendar->update(['teaching_type' => $teaching_type]);

      //ステータス整合性チェック
      $calendar = UserCalendar::where('id', $calendar_id)->first();

      if(($calendar->status=='rest' || $calendar->status=='lecture_cancel') && ($calendar->work==7 || $calendar->work==8)){
        //グループ or ファミリーの場合、参加生徒が一人以上いれば、fixに更新する
        foreach($calendar->members as $member){
          $s = Student::where('user_id', $member->user_id)->first();
          if(!isset($s)) continue;
          if($member->status=="fix"){
            //一人でも出席がいるのに、休みとなっていた
            $calendar->update(['status' => 'fix']);
            break;
          }
        }
      }
      if($work == 11){
        //TODO 期間講習の場合は下記のロジックで問題ないが、期間講習でない演習は、担当講師を期間講習から取得しない
        //演習の場合、同日の期間講習（work=10)の講師に担当をつける
        $season_lessons = UserCalendar::where('work' , 10)
                    ->where('start_time', '>', $item['ymd'].' 00:00:00')
                    ->where('start_time', '<', $item['ymd'].' 23:59:59')
                    ->where('place_floor_id', $item['place_id'])
                    ->findStatuses(['new','confirm'], true)
                    ->get();

        foreach($season_lessons as $season_lesson){
          $_member = UserCalendarMember::where('calendar_id' , $calendar_id)
            ->where('user_id' , $season_lesson->user_id)
            ->first();
          if(!isset($_member)){
            UserCalendarMember::create([
              'calendar_id' => $calendar_id,
              'status' => 'fix',
              'rest_type' => $item['cancel'],
              'remark' => $item['comment'],
              'rest_result' => '',
              'schedule_id' => 0,
              'place_floor_sheat_id' => $sheat_id,
              'user_id' => $season_lesson->user_id,
              'create_user_id' => 1
            ]);
          }
        }
      }
      return true;
    }
    /**
     * カレンダータグ登録
     * @param array $item
     * @return boolean
     */
    private function store_calendar_tag($model_id, $key, $val){
      return $this->store_tag($model_id, $key, $val, 'user_calendars');
    }
    private function store_calendar_setting_tag($model_id, $key, $val){
      return $this->store_tag($model_id, $key, $val, 'user_calendar_settings');
    }
    private function store_charge_student_tag($model_id, $key, $val){
      return $this->store_tag($model_id, $key, $val, 'charge_students');
    }
    private function store_tag($model_id, $key, $val, $model='user_calendars'){
      if(empty($model_id)) return false;
      if(empty($key)) return false;
      if(empty($val)) return false;
      switch($model){
        case 'charge_students':
          $items = ChargeStudentTag::where('charge_student_id', $model_id)
            ->where('tag_key', $key)
            ->where('tag_value', $val)->first();
          break;
        case 'user_calendars':
          $items = UserCalendarTag::where('calendar_id', $model_id)
            ->where('tag_key', $key)
            ->where('tag_value', $val)->first();
          break;
        case 'user_calendar_settings':
          $items = UserCalendarTagSetting::where('user_calendar_setting_id', $model_id)
            ->where('tag_key', $key)
            ->where('tag_value', $val)->first();
          break;
      }
      if(isset($item)){
        //すでに存在する場合は保存しない
        return false;
      }

      switch($model){
        case 'charge_students':
          return ChargeStudentTag::setTag($model_id, $key, $val, 1);
        case 'user_calendars':
          return UserCalendarTag::setTag($model_id, $key, $val, 1);
        case 'user_calendar_settings':
          return UserCalendarTagSetting::setTag($model_id, $key, $val, 1);
      }

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
        $attriubte = $this->get_save_general_attribute($key, "", $val);
        $val = $attriubte->attribute_value;
      }
      $items = UserTag::where('user_id', $user_id)
        ->where('tag_key', $key)
        ->where('tag_value', $val)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      return UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $key,
        'tag_value' => $val,
        'create_user_id' => 1
      ]);
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

      if(isset($items)){
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
      /*
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
      */
      return true;
    }

    /**
     * レクチャーマスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_lecture($item){
      $subject = GeneralAttribute::where('attribute_key', 'subject')->where('attribute_value' , $item['subject_id'])->first();
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
      \Log::channel('importlog')->warning($message);
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
    private function get_date($str_date){
      if(empty($str_date)) return null;
      if(strlen($str_date)!=10) return null;
      if($str_date=='0000-00-00') return null;
      return $str_date;
    }
    private function concealment(){
      $ret = [];
      $env = config('app.env');
      if($env!=="product"){
        //本番でない場合、保護者あてのメールを隠す
        $query = <<<EOT
        update common.users u inner join common.student_parents t on u.id = t.user_id set email=concat('yasui.hideo+p',t.id,'@gmail.com')
EOT;
        $ret[] = DB::update($query, []);
        @$this->remind("契約者のメールアドレスを秘匿(".$env.")", 'info', $this->logic_name);
      }
      if($env!=="product" && $env !== "staging"){
        //staging or productでない場合、講師あて、事務あてのメールを隠す
        $query = <<<EOT
          update common.users u inner join common.teachers t on u.id = t.user_id set email=concat('yasui.hideo+t',t.id,'@gmail.com')
EOT;
        $ret[] = DB::update($query, []);
        $query = <<<EOT
          update common.users u inner join common.managers t on u.id = t.user_id set email=concat('yasui.hideo+m',t.id,'@gmail.com')
          where u.id > 1
EOT;
        $ret[] = DB::update($query, []);
        @$this->remind("講師・事務のメールアドレスを秘匿(".$env.")", 'info', $this->logic_name);
      }
      return $ret;
    }
    private function test(){
      @$this->remind("test", 'info', 'test');
    }
}
