<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;
use Illuminate\Support\Facades\Session;
/**
 * App\Models\ActionLog
 *
 * @property int $id
 * @property string $server_name SERVER_NAME
 * @property string $server_ip SERVER_ADDR
 * @property string $method REQUEST_METHOD
 * @property string $client_ip REMOTE ADDRESS
 * @property string $session_id SESSION_ID
 * @property int $login_user_id login_user_id
 * @property string $user_agent HTTP USER AGENT
 * @property string $language HTTP ACCEPT LANGUAGE
 * @property string $url REQUEST_URI
 * @property string $referer HTTP REFERER
 * @property string $post_param post変数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $created_date
 * @property-read mixed $login_user_name
 * @property-read mixed $status_name
 * @property-read mixed $updated_date
 * @property-read \App\User $login_user
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog findMethods($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog searchWord($word)
 * @mixin \Eloquent
 */
class ActionLog extends Model
{
  use Common;
  protected $table = 'common.action_logs';
  protected $guarded = array('id');
  protected $appends = ['login_user_name', 'created_date', 'updated_date'];
  /**
   * 入力ルール
   */
  public static $rules = array(
    'server_name' => 'required',
    'server_ip' => 'required',
    'method' => 'required',
  );
  public function login_user(){
    return $this->belongsTo('App\User', 'login_user_id');
  }

  public function scopeFindMethods($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'method', $vals, $is_not);
  }
  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
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
  public function getLoginUserNameAttribute(){
    if(isset($this->login_user)){
      return $this->login_user->details()->name();
    }
    return "";
  }
  public function getStatusNameAttribute(){
    return $this->config_attribute_name('mail_status', $this->status);
  }

  static protected function add(Request $request, $login_user_id=null){
    $data = [];
    try {

      $data['server_name'] = $_SERVER['SERVER_NAME'];
      $data['server_ip'] = $_SERVER['SERVER_ADDR'];
      $data['url'] = url()->full();
      $data['method'] = $_SERVER['REQUEST_METHOD'];
      $data['referer'] = url()->previous();
      $data['client_ip'] = '-';
      if(isset($_SERVER['REMOTE_ADDR'])){
        $data['client_ip'] = $_SERVER['REMOTE_ADDR'];
      }
      $data['user_agent'] = '-';
      if(isset($_SERVER['HTTP_USER_AGENT'])){
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      }
      $data['language'] = '-';
      if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
        $data['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
      }
      $p = [];
      $data['session_id'] = Session::getId();
      $data['login_user_id'] = 0;
      if($login_user_id!=null)  $data['login_user_id'] = $login_user_id;

      foreach($request->all() as $key => $val){
        if($key=='password' || $key=='password_confirm') continue;
        $p[$key] = $val;
      }
      $data['post_param'] = json_encode($p);
      ActionLog::create($data);
    }
    catch(\Exception $e){
      \Log::warning("ActionLog::add Exception Error (".$e->getMessage().")");
    }
  }
}
