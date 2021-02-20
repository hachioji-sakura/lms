<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\EventUserMail;
class EventUser extends Milestone
{
  protected $table = 'lms.event_users'; //テーブル名を入力
  protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

  public static $rules = array( //必須フィールド
      'user_id' => 'required',
      'event_id' => 'required',
  );
  protected $appends = ['created_date', 'updated_date'];

  public function mails(){
    return $this->hasMany('App\Models\EventUserMail');
  }
  public function event(){
    return $this->belongsTo('App\Models\Event', 'event_id');
  }
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'user_id', 'user_id');
  }
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher', 'user_id', 'user_id');
  }
  public function manager(){
    return $this->belongsTo('App\Models\Manager', 'user_id', 'user_id');
  }
  public function parent(){
    return $this->belongsTo('App\StudentParent', 'user_id', 'user_id');
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
  public function getSendedDateAttribute(){
    if($this->status!='sended') return "";
    $m = $this->mails->sortByDesc('created_at')->first();
    if(empty($m)) return;
    return $m->updated_date;
  }
  public function getUserRoleAttribute(){
    return $this->user->role_name;
  }
  public function getTagsAttribute(){
    $ret = [];
    foreach($this->user->tags->whereIn('tag_key', ['lesson', 'grade']) as $tag){
      $ret[] = $tag->name();
    }
    return $ret;
  }
  public function to_inform(){
    $param = $this->event->toArray();
    $param['app_url'] = config('app.url');
    $param['event_id'] = $this->event->id;
    $param['event_template_id'] = $this->event->template->id;
    $param['url'] = $this->event->template->url;
    $param['user_name'] = $this->user_name;
    $res = $this->user->send_mail($this->event->title, $param, 'text', 'event_mail');
    if($this->is_success_response($res)){
      EventUserMail::create([
        'event_user_id' => $this->id,
        'mail_id' => $res['data']->id
      ]);
    }
  }
  public function getStatusNameAttribute(){
    $this->status_update();
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.mail_status')[$this->status])){
      $status_name = config('attribute.mail_status')[$this->status];
    }
    return $status_name;
  }
  public function status_update(){
    $m = $this->mails->sortByDesc('created_at')->first();
    if(empty($m)) return;
    $this->update(['status' => $m->mail->status]);
  }
}
