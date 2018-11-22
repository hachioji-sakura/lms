<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
}
