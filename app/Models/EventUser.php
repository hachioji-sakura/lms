<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
class EventUser extends Model
{
  protected $table = 'lms.event_users'; //テーブル名を入力
  protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

  public static $rules = array( //必須フィールド
      'user_id' => 'required',
      'event_id' => 'required',
  );

  public function event(){
    return $this->belongsTo('App\Models\Event', 'event_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
}
