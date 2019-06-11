<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralAttribute;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Milestone extends Model
{
  protected $table = 'milestones';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    return $this->attribute_name('milestone_type', $this->type);
  }
  public function scopeMydata($query, $val)
  {
      return $query->where('target_user_id', $val);
  }
  public function scopeStatus($query, $val)
  {
      return $query->where('status', $val);
  }
  public function scopeFindTypes($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'type', $vals, $is_not);
  }
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
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
  public function scopePagenation($query, $page, $line){
    $_line = $this->pagenation_line;
    if(is_numeric($line)){
      $_line = $line;
    }
    $_page = 0;
    if(is_numeric($page)){
      $_page = $page;
    }
    $_offset = $_page*$_line;
    if($_offset < 0) $_offset = 0;
    return $query->offset($_offset)->limit($_line);
  }
  public function target_user(){
    return $this->belongsTo('App\User', 'target_user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  protected function attribute_name($key, $value){
    $_attribute = GeneralAttribute::where('attribute_key', $key)->where('attribute_value', $value)->first();
    return $_attribute['attribute_name'];
  }
  protected function config_attribute_name($key, $value){
    $_lists = config('attribute.'.$key);
    return $_lists[$value];
  }
  public function details(){
    $item = $this;
    $item["type_name"] = $this->type_name();
    $item["create_user_name"] = $this->create_user->details()->name();
    $item["target_user_name"] = $this->target_user->details()->name();
    return $item;
  }
  public function send_mail($user_id, $title, $param, $type, $template){
    $controller = new Controller;
    $u = User::where('id', $user_id)->first();
    if(!isset($u)) return $controller->bad_request();
    $param['user'] = $u->details();
    $param['send_to'] = $param['user']->role;
    $res = $controller->send_mail($this->get_mail_address($param['user']), $title, $param, $type, $template);
    return $res;
  }
  private function get_mail_address($user){
    \Log::info("-----------------get_mail_address------------------");
    $email = '';
    \Log::info($user->role);
    if($user->role==='student'){
      $student_id = $user->id;
      $relations = StudentRelation::where('student_id', $student_id)->get();
      foreach($relations as $relation){
        //TODO 先にとれたユーザーを操作する親にする（修正したい）
        $user_id = $relation->parent->user->id;
        $email = $relation->parent->user->email;
        \Log::info("relation=".$user_id.":".$email);
        //TODO 安全策をとるテスト用メールにする
        //$email = 'yasui.hideo+u'.$user_id.'@gmail.com';
        break;
      }
    }
    else {
      $email = $user->email;
    }
    \Log::info("-----------------get_mail_address[$email]------------------");
    return $email;
  }
  protected function send_slack($message, $msg_type, $username=null, $channel=null) {
    $controller = new Controller;
    $res = $controller->send_slack($message, $msg_type, $username, $channel);
    return $res;
  }

}
