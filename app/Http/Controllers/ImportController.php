<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Student;
use App\Models\UserAlias;
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
      "courses" =>  "api_get_course",
      "textbooks" =>  "api_get_material",
      "charge_students" =>  "api_get_teacherstudent",
    ];
    //API auth token
    public $token = "7511a32c7b6fd3d085f7c6cbe66049e7";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $object)
    {
        set_time_limit(300);
        if(!array_key_exists($object, $this->api_endpoint)){
          return $this->send_json_response($this->bad_request());
        }
        $url = $this->api_domain.'/'.$this->api_endpoint[$object].'.php';
        $res = $this->call_api($request, $url);
        if(!$this->is_success_responce($res)){
          return $this->send_json_response($res);
        }
        $items = $res["data"];
        switch($object){
          case "courses":
            $res = $this->courses_import($items);
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
        }
        if(!$this->is_success_responce($res)){
          return $this->send_json_response($res);
        }
        return $this->send_json_response($this->api_responce());;
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
        echo $url;
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
    private function courses_import($items){
      try {
        DB::beginTransaction();
        $l=0, $s=0, $c=0;
        foreach($items as $item){
          if($this->store_attribute('lesson', $item['lesson_id'], $item['lesson_name'])) l++;
          if($this->store_attribute('subject', $item['subject_id'], $item['subject_name'])) s++;
          if($this->store_attribute('course', $item['course_id'], $item['course_name'])) c++;
        }
        DB::commit();
        return $this->api_responce(200, __FUNCTION___, "lesson[$l], subject[$s], course[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
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
        return $this->api_responce(200, __FUNCTION___, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
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
        return $this->api_responce(200, __FUNCTION___, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
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
        return $this->api_responce(200, __FUNCTION___, "count[$c]");
      }
      catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return $this->error_responce("Query Exception", $e->getMessage());
      }
      catch(\Exception $e){
        DB::rollBack();
        return $this->error_responce("DB Exception", $e->getMessage());
      }
    }

    /**
     * 講師情報登録
     * @param array $item
     * @return boolean
     */
    private function store_teacher($item){
      $item['email'] = $item['mail_address'];
      $items = Users::where('email', $item['email'])->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      $item["image_id"] = 3;
      $item['password'] = 'sakusaku';

      //認証情報登録
      $res = $this->user_create([
        "name" => $item['teacher_no'],
        "password" => $item['password'],
        "email" => $item['email'],
        "image_id" => $item['image_id'],
      ]);
      if($this->is_success_responce($res)){
        //講師情報登録
        $Teacher = new Teacher;
        $_item = $Teacher->fill([
          "name" => $item['teacher_name'],
          "kana" => $item['teacher_furigana'],
          "user_id" => $res["data"]->id,
        ])->save();

        //講師属性登録
        $this->store_user_alias($res["data"]->id, 'lesson1', $item['lesson_id']);
        $this->store_user_alias($res["data"]->id, 'lesson2', $item['lesson_id2']);
      }
    }
    /**
     * 生徒情報登録
     * @param array $item
     * @return boolean
     */
    private function store_student($item){
      $item['email'] = $item['cid'];
      $items = Users::where('email', $item['email'])->first();
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
      $item["birth_day"] = $item["birth_year"]."-".$item["birth_month"].'-'.$item["birth_day"];
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
          "birth_day" => $item['birth_day'],
          "gender" => $item['gender'],
          "user_id" => $res["data"]->id,
        ])->save();
        //生徒属性登録
        $this->store_user_alias($res["data"]->id, 'student_kind', $item['student_kind']);
        $this->store_user_alias($res["data"]->id, 'grade', $item['grade']);
        $this->store_user_alias($res["data"]->id, 'grade_adj', $item['grade_adj']);
        $this->store_user_alias($res["data"]->id, 'fee_free', $item['fee_free']);
        $this->store_user_alias($res["data"]->id, 'jyukensei', $item['jyukensei']);
        return true;
      }
      return false;
    }
    /**
     * 担当生徒登録
     * @param array $item
     * @return boolean
     */
    private function store_user_alias($student_no, $teacher_no, $course_id){
      $student = User::where('name', $student_no)->first()->student;
      if(isset($student)){
        return false;
      }
      $teacher = User::where('name', $teacher_no)->first()->teacher;
      if(isset($teacher)){
        return false;
      }
      $course = Attribute::course($course_id)->first();
      if(isset($course)){
        return false;
      }

      $items = ChargeStudent::where('student_id', $student->id)
        ->where('teacher_id', $teacher->id)
        ->where('course_id', $course->attribute_value)->first();

      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      $ChargeStudent = new ChargeStudent;
      $ChargeStudent->fill([
        "student_id" => $user_id,
        "teacher_id" => $key,
        "course_id" => $val,
        "create_user_id" => 1
      ])->save();
      return true;
    }
    /**
     * ユーザーエイリアス登録
     * @param array $item
     * @return boolean
     */
    private function store_user_alias($user_id, $key, $val){
      $items = UserAlias::where('user_id', $user_id)
        ->where('alias_key', $key)
        ->where('alias_value', $val)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      if(empty($user_id)) return false;
      if(empty($Key)) return false;
      if(empty($val)) return false;
      $UserAlias = new UserAlias;
      $UserAlias->fill([
        "user_id" => $user_id,
        "alias_key" => $key,
        "alias_value" => $val,
        "create_user_id" => 1
      ])->save();
      return true;
    }
    /**
     * 属性マスタ登録
     * @param array $item
     * @return boolean
     */
    private function store_attribute($key, $value, $name){
      $items = Attribute::where('attribute_key', $key)
        ->where('attribute_value', $value)->first();
      if(isset($items)){
        //すでに存在する場合は保存しない
        return false;
      }

      $model = new Attribute;
      $model->fill([
        "attribute_key" => $key,
        "attribute_value" => $value,
        "attribute_name" => $name,
        "create_user_id" => 1,
        ])->save();
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
