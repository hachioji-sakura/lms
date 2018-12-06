<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\GeneralAttribute;
use App\Models\Textbook;
use App\Models\TextbookTag;
use App\Models\Publisher;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\UserTag;
use App\Models\ChargeStudent;
use Illuminate\Http\Request;
use DB;
class ImportController extends UserController
{
    //事務管理システム側の情報
    //API URL: domain+endpoint+.php?query_string
    public $api_domain = "https://hachiojisakura.com/sakura-api";
    public $api_endpoint = [
      "students" =>  "api_get_student",
      "teachers" =>  "api_get_teacher",
      "general_attributes" =>  "api_get_course",
      "textbooks" =>  "api_get_material",
      "calendar" => "api_get_calendar",
      "charge_students" =>  "api_get_teacherstudent",
    ];
    //API auth token
    public $token = "7511a32c7b6fd3d085f7c6cbe66049e7";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $object='')
    {
      set_time_limit(600);
      if(empty($object)){
        $objects = ["general_attributes",
          "students",
          "teachers",
          "textbooks",
          "charge_students",
          "calendar",
        ];
        foreach($objects as $_object){
          $res = $this->import($request, $_object);
          if(!$this->is_success_responce($res)){
            return $this->send_json_response($res);
          }
        }
        return $this->send_json_response($res);
      }
      else {
        $res = $this->import($request, $object);
        return $this->send_json_response($res);
      }
    }
    public function import(Request $request, $object)
    {
        if(!array_key_exists($object, $this->api_endpoint)){
          return $this->bad_request();
        }
        $url = $this->api_domain.'/'.$this->api_endpoint[$object].'.php';
        $res = $this->call_api($request, $url);
        if(!$this->is_success_responce($res)){
          var_dump($res);
          return $this->error_responce("api error", $url);
        }
        $items = $res["data"];
        switch($object){
          case "general_attributes":
            $res = $this->general_attributes_import($items);
            break;
          case "students":
            $res = $this->students_import($items);
            break;
          case "teachers":
            $res = $this->teachers_import($items);
            break;
          case "charge_students":
            $res = $this->charge_students_import($items);
            break;
          case "textbooks":
            $res = $this->textbooks_import($items);
            break;
          case "calendar":
            $res = $this->calendars_import($items);
            break;
        }
        return $res;
    }
    /**
     * 実際にAPIを実行する処理。取得結果を配列にデコードして返却
     * @param Request $request
     * @param string $url
     * @return json
     */
    private function call_api(Request $request, string $url) {
        //$form = $request->all();
        $curl = curl_init();
        $query_string = http_build_query($request->query());
        if(!empty($query_string)){
          $url .= '?'.$query_string;
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
        //POSTの場合は、http_build_queryが不要、PUTは必要
        //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('api-token:'.$this->token));
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result,true);
    }

    /**
     * 事務管理システムから取得したデータを取り込み
     * @param array $items
     * @return boolean
     */
    private function general_attributes_import($items){
      try {
        DB::beginTransaction();
        $l=0;
        $s=0;
        $c=0;
        foreach($items as $item){
          if($this->store_general_attribute('lesson', $item['lesson_id'], $item['lesson_name'])) $l++;
          if($this->store_general_attribute('subject', $item['subject_id'], $item['subject_name'])) $s++;
          if($this->store_general_attribute('course', $item['course_id'], $item['course_name'])) $c++;
        }
        DB::commit();
        return $this->api_responce(200, __FUNCTION__, "lesson[$l], subject[$s], course[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
        return $this->api_responce(200, __FUNCTION__, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
        return $this->api_responce(200, __FUNCTION__, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
        echo "count=".count($items)."\n";
        foreach($items as $item){
          if($this->store_charge_student($item)) $c++;
        }
        DB::commit();
        return $this->api_responce(200, __FUNCTION__, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
        return $this->api_responce(200, __FUNCTION__, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
        return $this->api_responce(200, __FUNCTION__, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
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
      $items = User::where('email', $item['email'])->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      $item["image_id"] = 3;
      $item['password'] = 'sakusaku';
      $item['status'] = 0;
      if(intval($item['del_flag'])===2){
        $item['status'] = 9;
      }
      //認証情報登録
      $res = $this->user_create([
        "name" => $item['teacher_no'],
        "password" => $item['password'],
        "email" => $item['email'],
        "image_id" => $item['image_id'],
        "status" => $item['status'],
      ]);
      if($this->is_success_responce($res)){
        //講師情報登録
        $Teacher = new Teacher;
        $_item = $Teacher->fill([
          "name" => $item['teacher_name'],
          "kana" => $item['teacher_furigana'],
          "user_id" => $res["data"]->id,
          "create_user_id" => 1
        ])->save();

        //講師属性登録
        if($item['lesson_id']!="0") $this->store_user_tag($res["data"]->id, 'lesson', $item['lesson_id']);
        if($item['lesson_id2']!="0") $this->store_user_tag($res["data"]->id, 'lesson', $item['lesson_id2']);
        return true;
      }
      return false;
    }
    /**
     * 生徒情報登録
     * @param array $item
     * @return boolean
     */
    private function store_student($item){
      $item['email'] = $item['cid'];
      $items = User::where('email', $item['email'])->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }
      if(!is_numeric($item['gender'])){
        $item["image_id"] = 4;
        $item["gender"] = 3;
      }
      else {
        $item["gender"] = integer($item["gender"]);
        $item["image_id"] = $item["gender"];
      }
      if(strlen($item["birth_year"])===4 && $item["birth_month"]!="0" && $item["birth_day"] != "0"){
        $item["_birth_day"] = $item["birth_year"]."-".$item["birth_month"].'-'.$item["birth_day"];
      }
      else {
        $item["_birth_day"] = "1900-01-01";
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
      //認証情報登録
      $res = $this->user_create([
        "name" => $item['student_no'],
        "password" => $item['password'],
        "email" => $item['email'],
        "image_id" => $item['image_id'],
      ]);
      if($this->is_success_responce($res)){
        //生徒情報登録
        $Student = new Student;
        $_item = $Student->fill([
          "name_last" => $item['family_name'],
          "name_first" => $item['first_name'],
          "kana_last" => $item['kana_last'],
          "kana_first" => $item['kana_first'],
          "birth_day" => $item['_birth_day'],
          "gender" => $item['gender'],
          "user_id" => $res["data"]->id,
          "create_user_id" => 1,
        ])->save();
        //生徒属性登録
        $this->store_user_tag($res["data"]->id, 'student_kind', $item['student_kind']);
        $this->store_user_tag($res["data"]->id, 'grade', $item['grade']);
        $this->store_user_tag($res["data"]->id, 'grade_adj', $item['grade_adj']);
        $this->store_user_tag($res["data"]->id, 'fee_free', $item['fee_free']);
        $this->store_user_tag($res["data"]->id, 'jyukensei', $item['jyukensei']);
        return true;
      }
      return false;
    }
    /**
     * 担当生徒登録
     * @param array $item
     * @return boolean
     */
    private function store_charge_student($item){
      if(empty($item["teacher_no"]) || intval($item["teacher_no"])==0) return false;
      $student = User::where('name', $item["student_no"])->first();
      if(!isset($student)){
        return false;
      }
      $student = $student->student;
      $teacher = User::where('name', $item["teacher_no"])->first();
      if(!isset($teacher)){
        return false;
      }
      $teacher = $teacher->teacher;
      $items = ChargeStudent::where('student_id', $student->id)
        ->where('teacher_id', $teacher->id)->first();

      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      ChargeStudent::create([
        "student_id" => $student->id,
        "teacher_id" => $teacher->id,
        "body" => "course_id=".$item["course_id"],
        "create_user_id" => 1
      ]);
      return true;
    }
    /**
     * 担当生徒登録
     * @param array $item
     * @return boolean
     */
    private function store_calendar($item){
      if(empty($item["teacher_no"]) || intval($item["teacher_no"])==0) return false;
      $student = User::where('name', $item["student_no"])->first();
      if(!isset($student)){
        return false;
      }
      if(!isset($student->student) || !is_numeric($student->student->id)){
        //存在しない生徒
        return false;
      }
      $student = $student->student;
      $teacher = User::where('name', $item["teacher_no"])->first();
      if(!isset($teacher)){
        return false;
      }
      if(!isset($teacher->teacher) || !is_numeric($teacher->teacher->id)){
        //存在しない講師
        return false;
      }
      $teacher = $teacher->teacher;

      $items = ChargeStudent::where('student_id', $student->id)
        ->where('teacher_id', $teacher->id)->first();

      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      return true;
    }
    /**
     * ユーザータグ登録
     * @param array $item
     * @return boolean
     */
    private function store_user_tag($user_id, $key, $val){
      $items = UserTag::where('user_id', $user_id)
        ->where('tag_key', $key)
        ->where('tag_value', $val)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      if(empty($user_id)) return false;
      if(empty($Key)) return false;
      if(empty($val)) return false;
      UserTag::create([
        "user_id" => $user_id,
        "tag_key" => $key,
        "tag_value" => $val,
        "create_user_id" => 1
      ]);
      return true;
    }
    /**
     * ユーザータグ登録
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
        "textbook_id" => $textbook_id,
        "tag_key" => $key,
        "tag_value" => $val,
        "create_user_id" => 1
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
        "attribute_key" => $key,
        "attribute_value" => $value,
        "attribute_name" => $name,
        "create_user_id" => 1,
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
        "attribute_key" => $key,
        "attribute_value" => $value,
        "attribute_name" => $name,
        "create_user_id" => 1,
        ]);
    }
    /**
     * 教科書マスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_textbook($item){
      $items = Publisher::where('url', $item["publisher_id"])->first();
      $publishe_id = 0;
      if(isset($items)){
        $publishe_id = $items->id;
      }
      else {
        //supplier　→　publisher_nameを使う
        //出版社が存在しなければ追加
        $items = Publisher::create([
            'name' => $item['supplier'],
            'url' => $item['publisher_id'],
            'create_user_id' => 1
        ]);
        $publishe_id = $items->id;
      }
      $items = Textbook::where('url', $item["id"])->first();
      $textbook_id = 0;
      if(isset($items)){
        $textbook_id = $items->id;
      }
      else {
        if(empty($item["explain"])) $item["explain"] = "";
        if(empty($item["teika_price"])) $item["teika_price"] = 0;
        if(empty($item["publisher_price"])) $item["publisher_price"] = 0;
        if(empty($item["tewatashi_price1"])) $item["tewatashi_price1"] = 0;
        if(empty($item["tewatashi_price2"])) $item["tewatashi_price2"] = 0;
        if(empty($item["tewatashi_price3"])) $item["tewatashi_price3"] = 0;
        $items = Textbook::create([
          "name" => $item["name"],
          "selling_price" => str_replace(',','', $item["publisher_price"]),
          "list_price" => str_replace(',','', $item["teika_price"]),
          "price1" => str_replace(',','', $item["tewatashi_price1"]),
          "price2" => str_replace(',','', $item["tewatashi_price2"]),
          "price3" => str_replace(',','', $item["tewatashi_price3"]),
          "url" => $item["id"],
          "explain" => $item["explain"],
          "publisher_id" => $publishe_id,
          "create_user_id" => 1,
        ]);
        $textbook_id = $items->id;
      }
      if(!empty($item["subject"])){
        $_attr = $this->get_save_general_attribute('subject', '', $item["subject"]);
        $this->store_textbook_tag($textbook_id, "subject", $_attr->attribute_value);
      }
      if(!empty($item["level"])){
        $_attr = $this->get_save_general_attribute('level', '', $item["level"]);
        $this->store_textbook_tag($textbook_id, "level", $_attr->attribute_value);
      }
      if(!empty($item["grade"])){
        $_attr = $this->get_save_general_attribute('grade', '', $item["grade"]);
        $this->store_textbook_tag($textbook_id, "grade", $_attr->attribute_value);
      }

      return true;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rest.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $restdata = new Restdata;
        $form = $request->all();
        unset($form['_token']);
        $restdata->fill($form)->save();
        return redirect('/rest');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $items = Restdata::find($id);
        return $items->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $target = Restdata::find($id);
      return view('rest.create', ['form' => $target]);

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
        $items = Restdata::find($id);
        $form = $request->all();
        unset($form['_token']);
        $items->fill($form)->save();
        return redirect('/rest');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $items = Restdata::find($id)->delete();
        return redirect('/rest');
    }
}
