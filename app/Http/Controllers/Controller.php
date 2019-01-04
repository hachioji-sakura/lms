<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\CommonNotification;
use Illuminate\Http\Request;
use Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function send_json_response($json){
      return response($json, $json["status"])->header('Content-Type', 'application/json');
    }
    protected function api_response($status=200, $message="", $description="", $data=[]){
      $json = [
        "status" => $status,
        "message" => $message,
        "description" => $description,
        "data" => $data
      ];
      return $json;
    }
    protected function is_success_response($json){
      if($json["status"]===200) return true;
      if($json["status"]==="success") return true;
      return false;
    }
    protected function error_response($message="system error", $description=""){
      return $this->api_response(500, $message, $description);
    }
    protected function notfound($message="not found", $description=""){
      return $this->api_response(404, $message, $description);
    }
    protected function bad_request($message="bad request", $description=""){
      return $this->api_response(400, $message, $description);
    }
    protected  function forbidden($message="forbidden", $description=""){
      return $this->api_response(403, $message, $description);
    }
    protected function send_mail($to, $title, $param, $type, $template)
    {
      if(config('app.debug')){
        //開発環境の場合、本来の送信先は使わない
        $to = config('app.debug_mail');
      }
      $title = '【'.config('app.name').'】'.$title;
      $res = Mail::to($to)->send(new CommonNotification($title, $param, $type, $template));
      $this->send_slack("メール送信:\n".$to."\n".$title, "info", "send_mail");
      return "mail_send";
    }
    protected function send_slack($message, $msg_type, $username=null, $channel=null) {
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
    protected function call_api(Request $request, string $url) {
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
        if(!empty($this->token)){
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('api-token:'.$this->token));
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result,true);
    }

}
