<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\CommonNotification;
use Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function send_json_response($json){
      return response($json, $json["status"])->header('Content-Type', 'application/json');
    }
    protected function api_responce($status=200, $message="", $description="", $data=[]){
      $json = [
        "status" => $status,
        "message" => $message,
        "description" => $description,
        "data" => $data
      ];
      return $json;
    }
    protected function is_success_responce($json){
      if($json["status"]===200) return true;
      if($json["status"]==="success") return true;
      return false;
    }
    protected function error_responce($message="system error", $description=""){
      return $this->api_responce(500, $message, $description);
    }
    protected function notfound($message="not found", $description=""){
      return $this->api_responce(404, $message, $description);
    }
    protected function bad_request($message="bad request", $description=""){
      return $this->api_responce(400, $message, $description);
    }
    protected  function forbidden($message="forbidden", $description=""){
      return $this->api_responce(403, $message, $description);
    }
    protected function send_mail($to, $title, $param, $type, $template)
    {
      $title = '【'.config('app.name').'】'.$title;
      $res = Mail::to($to)->send(new CommonNotification($title, $param, $type, $template));
      return "mail_send";
    }
    protected function send_slack($message, $msg_type, $channel=null) {
      $icon = ":speech_ballon";
      switch($msg_type){
        case "info":
          $icon = ":speech_ballon";
          break;
        case "error":
          $icon = ":no_entry";
          break;
        case "warning":
          $icon = ":warning";
          break;
      }
    	return $this->_send_slack($message, $icon, $channel);
    }
    private function _send_slack($message, $icon=null,$channel=null, $username=null,  $url=null) {
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

}
