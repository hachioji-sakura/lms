<?php
namespace App\Models\Traits;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAttribute;
use App\Models\MailLog;

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
  public function _remind_mail($to, $title, $param, $type, $template, $locale, $send_schedule=''){
    if(empty($send_schedule)) $send_schedule=date('Y-m-d H:i:s');
    //同じリマインドは登録しない
    $already_mail_log = Maillog::where('to_address', $to)
                          ->where('template', $template)
                          ->where('subject', $title)
                          ->where('send_schedule', $send_schedule)
                          ->first();
    if(isset($already_mail_log)){
      return $this->error_response("すでに登録済み", "");
    }

    $mail_log_res = MailLog::add(config('mail.from')['address'], $to, $title, $param, $type, $template, $locale, 'new', $send_schedule);
    return $mail_log_res;
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
    $form = [];
    if(isset($this->s3_alias)) $form['s3_alias'] = '';
    if(isset($this->s3_url)) $form['s3_url'] = '';
    $res = $controller->s3_delete($s3_url);
    $this->update($form);
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
  public function dateweek_format($date, $format = "n月j日"){
    if(empty($date)) return "-";

    $date = str_replace('/', '-', $date);
    $date = str_replace('年', '-', $date);
    $date = str_replace('月', '-', $date);
    $date = str_replace('日', '', $date);
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
    if(gettype($vals) == "string" || gettype($vals) == "integer") $vals = explode(',', $vals.',');

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
  protected function _date_label($date, $format='Y年m月d日 H:i'){
    return date($format, strtotime($date));
  }
  public function getCreatedDateAttribute(){
    return $this->_date_label($this->created_at);
  }
  public function getUpdatedDateAttribute(){
    return $this->_date_label($this->updated_at);
  }
  public function created_at_label($format='Y年m月d日 H:i'){
    return $this->_date_label($this->created_at, $format);
  }
  public function updated_at_label($format='Y年m月d日 H:i'){
    return $this->_date_label($this->updated_at, $format);
  }
  public function attribute_name($key, $value){
    $_attribute = GeneralAttribute::get_item($key, $value);
    if(isset($_attribute))  return $_attribute['attribute_name'];
    return "";
  }
  public function config_attribute_name($key, $value){
    $_lists = config('attribute.'.$key);
    if(isset($_lists) && isset($_lists[$value])) return $_lists[$value];
    return "";
  }
  public function is_enable_status($status=""){
    if(empty($status)) $status = $this->status;
    if($status=='cancel') return false;
    if($this->is_rest_status($status)==true) return false;
    return false;
  }
  public function is_rest_status($status=""){
    if(empty($status)) $status = $this->status;
    if($status=="rest") return true;
    if($status=="lecture_cancel") return true;
    if($status=="absence") return true;
    return false;
  }
  public function is_cancel_status($status=""){
    if(empty($status)) $status = $this->status;
    if($status==="lecture_cancel" || $status==="cancel" || $status==="rest"){
      return true;
    }
    return false;
  }
  public function is_last_status($status=""){
    if(empty($status)) $status = $this->status;
    if($this->is_rest_status($status)) return true;
    if($status==="cancel" || $status==="presence"){
      return true;
    }
    return false;
  }
  public function get_course_minutes($from_time, $to_time){
    return $course_minutes = intval(strtotime($to_time) - strtotime($from_time))/60;
  }
  public function get_search_word_array($str_search_word){
    //$search_words = explode(' ', rawurldecode(urlencode($str_search_word)));
    $search_words = explode(' ', (($str_search_word)));
    return $search_words;
  }
  public function has_tag($key, $val=""){
    if(!isset($this->tags)) return null;
    $tags = $this->tags;
    foreach($tags as $tag){
      if(empty($val) && $tag->tag_key==$key) return true;
      if($tag->tag_key==$key && $tag->tag_value==$val) return true;
    }
    return false;
  }
  public function get_tag($key, $val=""){
    if(!isset($this->tags)) return null;
    $item = $this->tags->where('tag_key', $key);
    if(!empty($val)) $item = $item->where('tag_value', $val);
    $item = $item->first();
    if(isset($item)){
      return $item;
    }
    return null;
  }
  public function get_tags($key){
    if(!isset($this->tags)) return null;
    $item = $this->tags->where('tag_key', $key);
    if(isset($item)){
      return $item;
    }
    return null;
  }
  public function get_tag_name($tag_key, $val=""){
    $tag =  $this->get_tag($tag_key, $val);
    if(isset($tag)){
      return $tag->name();
    }
    return "";
  }
  public function get_tag_value($tag_key, $val=""){
    $tag =  $this->get_tag($tag_key, $val);
    if(isset($tag)){
      return $tag->tag_value;
    }
    return "";
  }
  public function get_tags_name($tag_key){
    $tags =  $this->get_tags($tag_key);
    if(isset($tags)){
      $ret = "";
      foreach($tags as $tag){
        $ret .= $tag->name().',';
      }
      return trim($ret, ',');
    }
    return "";
  }
  public function get_tags_value($tag_key){
    $tags =  $this->get_tags($tag_key);
    if(isset($tags)){
      $ret = "";
      foreach($tags as $tag){
        $ret .= $tag->tag_value.',';
      }
      return trim($ret, ',');
    }
    return "";
  }

  public function scopeSearchTags($query, $tags)
  {
    if(!isset($tags)) return $query;
    if(!isset($this->tags)) return $query;
    return $query->whereHas('tags', function($query) use ($tags) {
        $query = $query->where(function($query)use($tags){
          foreach($tags as $tag){
            $query = $query->orWhere(function($query)use($tag){
              if(!empty($tag["tag_key"]) && !empty($tag["tag_value"])){
                $query->where('tag_key', $tag["tag_key"])->where('tag_value', $tag["tag_value"]);
              }
            });
          }
        });
    });
  }
  public function term_format($from, $to, $format='Y-m-d'){
    $start_hour_minute = date($format,  strtotime($from));
    $end_hour_minute = date($format,  strtotime($to));
    return $start_hour_minute.'～'.$end_hour_minute;
  }
}
