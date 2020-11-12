<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
class EventUser extends Milestone
{
  protected $table = 'lms.event_users'; //テーブル名を入力
  protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

  public static $rules = array( //必須フィールド
      'user_id' => 'required',
      'event_id' => 'required',
  );
  protected $appends = ['created_date', 'updated_date'];

  public function event(){
    return $this->belongsTo('App\Models\Event', 'event_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function student(){
    return $this->belongsTo('App\Student', 'user_id');
  }
  public function teacher(){
    return $this->belongsTo('App\Teacher', 'user_id');
  }
  public function manager(){
    return $this->belongsTo('App\Manager', 'user_id');
  }
  public function parent(){
    return $this->belongsTo('App\StudentParent', 'user_id');
  }
  public function getUserNameAttribute(){
    return $this->user->details()->name();
  }
  public function getGradeAttribute(){
    return $this->user->get_tags_name('grade');
  }
  public function getLessonAttribute(){
    return $this->user->get_tags_name('lesson');
  }
  public function getUrlAttribute(){
    return $this->user->get_url();
  }
  public function getTagsAttribute(){
    $ret = [];
    foreach($this->user->tags->whereIn('tag_key', ['lesson', 'grade']) as $tag){
      $ret[] = $tag->name();
    }
    return $ret;
  }
  public function getStatusNameAttribute(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.mail_status')[$this->status])){
      $status_name = config('attribute.mail_status')[$this->status];
    }
    return $status_name;
  }

}
