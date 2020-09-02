<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use App\Mail\CommonNotification;
use App\User;
use App\Models\MailLog;
use Illuminate\Http\Request;
use Mail;
use App;
use DB;

class Controller extends BaseController
{
  //API auth token
  public $token = '7511a32c7b6fd3d085f7c6cbe66049e7';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function send_json_response($json){
      return response($json, $json["status"])->header('Content-Type', 'application/json');
    }
    public function api_response($status=200, $message="", $description="", $data=[]){
      $json = [
        "status" => $status,
        "message" => $message,
        "description" => $description,
        "data" => $data
      ];
      return $json;
    }
    public function is_success_response($json){
      if(!isset($json["status"])) return false;
      if($json["status"]==="0") return true;
      if($json["status"]===200) return true;
      if($json["status"]==="success") return true;
      return false;
    }
    public function error_response($message="system error", $description=""){
      return $this->api_response(500, $message, $description);
    }
    public function notfound($message="not found", $description=""){
      return $this->api_response(404, $message, $description);
    }
    public function bad_request($message="bad request", $description=""){
      return $this->api_response(400, $message, $description);
    }
    public  function forbidden($message="forbidden", $description=""){
      return $this->api_response(403, $message, $description);
    }
    public function send_mail($to, $title, $param, $type, $template, $locale="ja")
    {
      App::setLocale($locale);
      //TODO いったんすべてサポートに送信
      $is_send_support_mail = false;
      /*
      $send_support_mail_tamplates = [
        'trial', 'trial_confirm',
        'register', 'calendar_correction', 'calendar_month_work',
        'calendar_rest', 'calendar_new',
        'ask_lecture_cancel_new', 'ask_lecture_cancel_commit', 'ask_lecture_cancel_cancel',
        'ask_recess_commit', 'ask_unsubscribe_commit',
      ];
      */
      $is_send_support_mail = true;
      /*
      foreach($send_support_mail_tamplates as $t){
        if($t == $template){
          $is_send_support_mail = true;
          break;
        }
      }
      */
      //TODO 2020.06.05 システム名を除去する（戻す可能性があるのでコメントあうとにしておく）
      //$title = '【'.__('labels.system_name').'】'.$title;
      $this->send_slack("メール送信:\n".$to."\n".$title, "info", "send_mail");
      \Log::info("メール送信:\n".$to."\n".$title);

      if(config('app.env')==="develop"){
        //開発環境の場合、本来の送信先は使わない
        $to = config('app.debug_mail');
      }
      $mail_log_res = MailLog::add(config('mail.from')['address'], $to, $title, $param, $type, $template, $locale);
      if($this->is_success_response($mail_log_res)){
        if(isset($mail_log_res['data'])){
          $mail_log_res['data']->send();
          return $mail_log_res;
        }
      }
      return null;
    }
    public function send_slack($message, $msg_type, $username=null, $channel=null) {
      if(empty($message)) return false;
      $icon = ":speech_ballon";
      switch($msg_type){
        case "info":
          $icon = ":speech_ballon";
          break;
        case "success":
          $icon = ":ok";
          break;
        case "error":
          $icon = ":no_entry";
          break;
        case "warning":
          $icon = ":warning";
          break;
      }
    	return $this->_send_slack($message, $icon, $username, $channel);
    }
    private function _send_slack($message, $icon=null, $username=null, $channel=null, $url=null) {
      if(empty($url)) $url = config('services.slack.endpoint');
      if(empty($channel)) $channel = config('services.slack.channel');
      if(empty($username)) $username = config('app.name');
    	$_request = array(
      	"channel" =>  $channel,
      	'username' => $username,
      	'text' => $message,
      	"icon_emoji" =>  $icon
    	);
    	$options = array(
    	'http' => array(
    		"protocol_version" => "1.1",
    		'method' => 'POST',
    		'header' => 'Content-Type: application/json',
    		'content' => json_encode($_request),
    	),
    	'ssl' => array(
    			'verify_peer' => false,
    			'verify_peer_name' => false
    		)
    	);
      if(config('app.env')==="local"){
        return true;
      }
    	$response = file_get_contents($url, false, stream_context_create($options));
    	if($response === 'ok') return true;
    	return false;
    }
    /**
     * 実際にAPIを実行する処理。取得結果を配列にデコードして返却
     * @param Request $request
     * @param string $url
     * @return json
     */
    public function call_api(Request $request, string $url, string $type="GET", $data=null) {
      //$form = $request->all();
      if(config('app.env')==="local"){
        return $this->api_response();
      }

      $curl = curl_init();
      $query_string = http_build_query($request->query());
      if(!empty($query_string)){
        $url .= '?'.$query_string;
      }
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
      if($type!=="GET" && isset($data)){
        curl_setopt($curl, CURLOPT_POSTFIELDS, ($data));
      }
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
      //POSTの場合は、http_build_queryが不要、PUTは必要
      //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
      if(!empty($this->token)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Api-Token:'.$this->token));
      }
      $result = curl_exec($curl);
      curl_close($curl);
      return json_decode($result,true);
    }
    /**
     * 期限付きtokenの生成
     * デフォルトの期限は、24時間
     * @param  int $limit_second
     * @param  int $key_length
     * @return string
     */
    public function create_token($limit_second=86400, $key_length=32){
      $key = str_random($key_length);
      $expire = time() + $limit_second;
      return $key.$expire;
     }
     /**
      * 期限付きtokenの期限判定
      * @param  string $token
      * @param  int $key_length
      * @return boolean
      */
     public function is_enable_token($token, $key_length=32){
       $expire = substr($token, $key_length);
       if(intval($expire)-time() > 0){
         return true;
       }
       return false;
     }
     public function token_test(Request $request, $key)
     {
       echo "判定[".$this->is_enable_token($key)."]";
       return "key=".$key;
     }

     public function s3_upload($request_file, $save_folder=""){
       $path = Storage::disk('s3')->putFile($save_folder, $request_file, 'public');
       $s3_url = Storage::disk('s3')->url(config('aws_s3.bucket')."/".$path);
       $this->send_slack("ファイルアップロード:\n".$path."\n".$s3_url, "info", "s3_upload");
       return ['url' => $s3_url, 'path' => $path];
     }
     public function s3_delete($s3_url){
       $path = explode('/', $s3_url);
       $file = $path[count($path)-2].'/'.$path[count($path)-1];
       //Path=Rootバケット/フォルダ名/ファイル名となっている前提
       $ret = Storage::disk('s3')->delete($file);
       $this->send_slack("アップロードファイル削除:\n".$file, "info", "s3_delete");
       return $ret;
     }
     //Googleカレンダーから祝日を取得
     public function getHolidays() {
       $url = "https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv";

       //JSON形式で取得した情報を配列に変換
       $results = file_get_contents($url);
       $results = mb_convert_encoding($results, 'UTF-8', 'sjis-win');
       $temp = tmpfile();
       $csv  = array();

       fwrite($temp, $results);
       rewind($temp);

       while (($results = fgetcsv($temp, 0, ",")) !== FALSE) {
           $csv[] = $results;
       }
       fclose($temp);

       //配列として祝日を返す
       return $csv;
     }
     public function transaction($request, $callback, $logic_name, $__file, $__function, $__line){
         try {
           DB::beginTransaction();
           $res = $callback();
           if($this->is_success_response($res)){
             DB::commit();
           }
           else {
             DB::rollBack();
           }
           // 二重送信対策
           if($request!=null){
             $request->session()->regenerateToken();
           }
           return $res;
         }
         catch (\Illuminate\Database\QueryException $e) {
             DB::rollBack();
             $this->send_slack($logic_name.'エラー:'.$e->getMessage(), 'error', $logic_name);
             return $this->error_response('Query Exception', '['.$__file.']['.$__function.'['.$__line.']'.'['.$e->getMessage().']');
         }
         catch(\Exception $e){
             DB::rollBack();
             $this->send_slack($logic_name.'エラー:'.$e->getMessage(), 'error', $logic_name);
             return $this->error_response('DB Exception', '['.$__file.']['.$__function.'['.$__line.']'.'['.$e->getMessage().']');
         }
     }
}
