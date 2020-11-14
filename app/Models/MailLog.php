<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;
use App\Mail\CommonNotification;

use View;
use App;
use Mail;
class MailLog extends Model
{
  use Common;
  protected $table = 'common.mails';
  protected $guarded = array('id');
  protected $appends = ['status_name', 'locale_name', 'created_date', 'updated_date'];

  /**
   * 入力ルール
   */
  public static $rules = array(
      'from_address' => 'required',
      'to_address' => 'required',
      'subject' => 'required',
      'type' => 'required',
  );
  public function scopeFindTemplates($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'template', $vals, $is_not);
  }
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }

  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('body','like',$_like)
              ->orWhere('to_address','like',$_like)
              ->orWhere('subject','like',$_like);
      }
    });
    return $query;
  }
  public function getStatusNameAttribute(){
    return $this->config_attribute_name('mail_status', $this->status);
  }
  public function getLocaleNameAttribute(){
    $_temp = ['ja' => '日本語', 'en' => '英語'];
    if(isset($_temp[$this->locale])) return $_temp[$this->locale];
    return $this->locale;
  }

  static protected function add($from, $to, $title, $param, $type, $template, $locale, $status='new', $send_schedule=''){
    $body = View::make('emails.'.$template.'_'.$type)->with($param)->render();
    if(empty($send_schedule)) $send_schedule = date('Y-m-d H:i:s');
    $src = [];
    $dst = [];
    $body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    foreach($param as $key=>$val){
      if(gettype($val)=='array') continue;
      if(gettype($val)!='string' && gettype($val) !='integer' && gettype($val)!='double') continue;
      $src[] = '#'.$key.'#';
      $dst[] = $val;
    }
    $body = str_replace($src, $dst, $body);
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
    $controller = new Controller;
    $t = date("Y-m-d H:i:s",strtotime("-1 minute"));
    //2重送信チェック(1分前に登録済みかどうか）
    $already_mail_log = Maillog::where('to_address', $to)
                          ->where('template', $template)
                          ->where('locale', $locale)
                          ->where('subject', $title)
                          ->where('body', $body)
                          ->where('created_at', '>' , $t)
                          ->first();
    if(isset($already_mail_log)){
      return $controller->error_response("すでに登録済み", "");
    }
    $mail_log = MailLog::create($create_form);
    return $controller->api_response(200, "", "", $mail_log);
  }
  public function change($form){
    $fields = ["subject", "body", "send_schedule", "type", "from_address", "to_address"];
    $update_form = [];
    foreach($form as $key => $val){
      if(!isset($val)) continue;
      $update_form[$key] = $val;
    }
    $this->update($update_form);
    return $this;
  }
  public function send(){
    if($this->status != 'new') return false;
    if(strtotime($this->send_schedule) > strtotime('now')) return false;
    //TODO いったんすべてサポートに送信
    $is_send_support_mail = true;
    /*
    $send_support_mail_tamplates = [
      'trial', 'trial_confirm',
      'register', 'calendar_correction', 'calendar_month_work',
      'calendar_rest', 'calendar_new',
      'ask_lecture_cancel_new', 'ask_lecture_cancel_commit', 'ask_lecture_cancel_cancel',
      'ask_recess_commit', 'ask_unsubscribe_commit',
    ];
    */
    /*
    foreach($send_support_mail_tamplates as $t){
      if($t == $template){
        $is_send_support_mail = true;
        break;
      }
    }
    */
    try {
      App::setLocale($this->locale);
      $data = [];
      $item = $this;
      if(config('app.env')!=="local"){
        Mail::raw($this->body, function($mail) use ($item, $is_send_support_mail){
          $mail = $mail->to($item->to_address);
          $mail = $mail->subject($item->subject);
          if($is_send_support_mail==true){
            $mail = $mail->cc(config('app.support_mail'));
          }
        });
      }
      $this->update(['status' => 'sended']);
    }
    catch(\Exception $e){
      $this->update(['status' => 'error']);
      $this->send_slack('メール送信エラー:'.$e->getMessage(), 'error', 'Controller.send_mail');
    }
    return true;
  }

  static protected function all_send(){
    $maillogs = Maillog::where('status' , 'new')->where('send_schedule', '<', date('Y-m-d H:i:s'))->get();
    foreach($maillogs as $maillog){
      $maillog->send();
    }
  }
}
