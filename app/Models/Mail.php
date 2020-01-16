<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;
class Mail extends Model
{
  protected $connection = 'mysql_common';
  protected $table = 'common.mails';
  protected $guarded = array('id');
  /**
   * 入力ルール
   */
  public static $rules = array(
      'from_address' => 'required',
      'to_address' => 'required',
      'subject' => 'required',
      'type' => 'required',
  );
  public functino status_name(){
    $_temp = ['new' => '新規登録', 'send' => '送信中', 'sended' => '送信済み', 'cancel' => 'キャンセル'];
    return $_temp[$this->status];
  }
  static protected function add($form){
    $mail = Mail::create([
    ]);
    return $mail;
  }

  public function send_mail($to, $title, $param, $type, $template, $locale="ja"){
    $u = User::where('id', $user_id)->first();
    $mail = $u->get_mail_address();
    if(!isset($u)) return $controller->bad_request();
    $param['user'] = $u->details();
    $param['send_to'] = $param['user']->role;
    $res = $this->_send_mail($mail, $title, $param, $type, $template, $u->get_locale());
    return $res;
  }

}
