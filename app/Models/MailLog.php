<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;
use App\Mail\CommonNotification;
use View;
use Mail;
class MailLog extends Model
{
  use Common;
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
  public function status_name(){
    return $this->config_attribute_name('mail_status', $this->status);
  }
  public function locale_name(){
    $_temp = ['ja' => '日本語', 'en' => '英語'];
    if(isset($_temp[$this->locale])) return $_temp[$this->locale];
    return $this->locale;
  }
  static protected function add($from, $to, $title, $param, $type, $template, $locale, $status='new', $send_schedule=''){
    $body = View::make('emails.'.$template.'_'.$type)->with($param)->render();
    if(empty($send_schedule)) $send_schedule = date('Y-m-d H:i:s');
    $create_form = [
      'from_address' => $from,
      'to_address' => $to,
      'subject' => $title,
      'type' => $type,
      'template' => $template,
      'body' => $body,
      'locale' => $locale,
      'send_schedule' => $send_schedule,
      'status' => $status
    ];
    $mail_log = MailLog::create($create_form);
    return $mail_log->api_response(200, "", "", $mail_log);

  }
  public function details(){
    $item = $this;
    $item["status_name"] = $this->status_name();
    $item["locale_name"] = $this->locale_name();
    $item["created_date"] = $this->created_at_label();
    $item["updated_date"] = $this->updated_at_label();
    return $item;
  }
}
