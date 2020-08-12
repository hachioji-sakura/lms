<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;
use Illuminate\Support\Facades\Session;
class ActionLog extends Model
{
  use Common;
  protected $connection = 'mysql_common';
  protected $table = 'common.action_logs';
  protected $guarded = array('id');
  /**
   * 入力ルール
   */
  public static $rules = array(
    'server_name' => 'required',
    'server_ip' => 'required',
    'method' => 'required',
  );
  public function scopeFindMethods($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'method', $vals, $is_not);
  }
  public function scopeSearchWord($query, $word){
    $search_words = explode(' ', $word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('session_id','like',$_like)
              ->orWhere('client_ip','like',$_like)
              ->orWhere('url','like',$_like)
              ->orWhere('referer','like',$_like)
              ->orWhere('post_param','like',$_like);
      }
    });
    return $query;
  }
  public function login_user(){
    return $this->belongsTo('App\User', 'login_user_id');
  }
  static protected function add($login_user_id=null){
    $data = [];
    try {
      $data['server_name'] = $_SERVER['SERVER_NAME'];
      $data['server_ip'] = $_SERVER['SERVER_ADDR'];
      $data['url'] = url()->full();
      $data['method'] = $_SERVER['REQUEST_METHOD'];
      $data['referer'] = url()->previous();
      $data['client_ip'] = $_SERVER['REMOTE_ADDR'];
      $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      $data['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
      $p = [];
      $data['session_id'] = Session::getId();
      $data['login_user_id'] = $login_user_id;
      foreach($_POST as $key => $val){
        if($key=='password' || $key=='password_confirm') continue;
        $p[$key] = $val;
      }
      $data['post_param'] = json_encode($p);
      ActionLog::create($data);
    }
    catch(\Exception $e){
    }
  }
  public function details(){
    $item = $this;
    $item["created_date"] = $this->created_at_label();
    $item["updated_date"] = $this->updated_at_label();
    if(isset($this->login_user)){
      $item["login_user_name"] = $this->login_user->details()->name();
    }
    return $item;
  }
}
