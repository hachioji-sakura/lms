<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\EventUserMail;
use App\Models\Traits\Common;

class EventUser extends Milestone
{
  use Common;

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
    return $this->belongsTo('App\Models\Student', 'user_id');
  }
  public function teacher(){
    return $this->belongsTo('App\Models\Teacher', 'user_id');
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
  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeSearchWord($query, $word)
  {
    $search_words = $this->get_search_word_array($word);
    return $query->whereHas('student', function($query) use ($search_words) {
        $query = $query->where(function($query)use($search_words){
          foreach($search_words as $_search_word){
            $_like = '%'.$_search_word.'%';
            $query = $query->orWhere('name_last','like', $_like)
              ->orWhere('name_first','like', $_like)
              ->orWhere('kana_last','like', $_like)
              ->orWhere('kana_first','like', $_like);
            }
        });
    });
  }

  public function to_inform(){
    $access_key = $this->create_token();
    $param = $this->event->toArray();

    $param['url'] = $this->event->template->url;
    $param['app_url'] = config('app.url');
    $param['event_id'] = $this->event->id;
    $param['access_key'] = $access_key;
    $param['event_template_id'] = $this->event->template->id;
    $param['user_name'] = $this->user_name;
    $param['user_id'] = $this->user->id;
    $param['event_user_id'] = $this->id;
    $param['user_details_id'] = $this->user->details()->id;
    $this->update(['access_key' => $access_key]);
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
