<?php
namespace App\Models\Traits;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

trait Common
{
  public function send_json_response($json){
    $controller = new Controller;
    return $controller->send_json_response($json);
  }
  public function api_response($status=200, $message="", $description="", $data=[]){
    $controller = new Controller;
    return $controller->api_response($status, $message, $description, $data);
  }
  public function is_success_response($json){
    $controller = new Controller;
    return $controller->is_success_response($json);
  }
  public function error_response($message="system error", $description=""){
    $controller = new Controller;
    return $controller->error_response($message, $description);
  }
  public function notfound($message="not found", $description=""){
    $controller = new Controller;
    return $controller->notfound($message, $description);
  }
  public function bad_request($message="bad request", $description=""){
    $controller = new Controller;
    return $controller->bad_request($message, $description);
  }
  public  function forbidden($message="forbidden", $description=""){
    $controller = new Controller;
    return $controller->forbidden($message, $description);
  }
  public function _send_mail($to, $title, $param, $type, $template, $locale){
    $controller = new Controller;
    $res = $controller->send_mail($to, $title, $param, $type, $template, $locale);
    return $res;
  }
  public function send_slack($message, $msg_type, $username=null, $channel=null) {
    $controller = new Controller;
    $res = $controller->send_slack($message, $msg_type, $username, $channel);
    return $res;
  }
  public function s3_upload($request_file, $save_folder=""){
    $controller = new Controller;
    $res = $controller->s3_upload($request_file, $save_folder);
    return $res;
  }
  public function s3_delete($s3_url){
    $controller = new Controller;
    $res = $controller->s3_delete($s3_url);
    $this->update([
      's3_alias' => '',
      's3_url' => '',
    ]);
    return $res;
  }
  public function create_token($limit_second=86400){
    $controller = new Controller;
    $res = $controller->create_token($limit_second);
    return $res;
  }
  public function call_api($url, $method, $data){
    $req = new Request;
    $controller = new Controller;
    $res = $controller->call_api($req, $url, $method, $data);
    return $res;
  }
  public function dateweek_format($date){
    if(empty($date)) return "-";
    $format = "n月j日";
    $weeks = config('week');
    if(app()->getLocale()=='en'){
      $format = "n/j";
      $weeks = config('week_en');
    }
    $d = date($format,  strtotime($date));
    $d .= '('.$weeks[date('w',  strtotime($date))].')';
    return $d;
  }
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
    if(gettype($vals) == "string") $vals = explode(',', $vals.',');

    if(count($vals) > 0){
      if($is_not===true){
        $query = $query->whereNotIn($field, $vals);
      }
      else {
        $query = $query->whereIn($field, $vals);
      }
    }
    return $query;
  }
}
