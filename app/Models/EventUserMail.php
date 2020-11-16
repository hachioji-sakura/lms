<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;

class EventUserMail extends Milestone
{
  protected $table = 'lms.event_user_mails'; //テーブル名を入力
  protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

  public static $rules = array( //必須フィールド
      'user_id' => 'required',
      'event_id' => 'required',
  );
  protected $appends = ['created_date', 'updated_date'];

  public function mail(){
    return $this->belongsTo('App\Models\MailLog', 'mail_id');
  }
  public function event_user(){
    return $this->belongsTo('App\Models\EventUser', 'event_user_id');
  }

}
