<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
use App\Models\Holiday;
use App\Models\LessonRequest;
use App\Models\LessonRequestDate;
use App\Models\Place;
class Event extends Milestone
{
    //
    protected $table = 'lms.events';
    protected $guarded = array('id');
    protected $appends = ['create_user_name', 'template_title', 'event_term', 'response_term', 'created_date', 'updated_date'];

    public static $rules = array( //必須フィールド
        'event_template_id' => 'required',
        'title' => 'required',
        'event_from_date' => 'required',
        'event_to_date' => 'required',
        'response_from_date' => 'required',
        'response_to_date' => 'required'
    );

    public function template(){
      return $this->belongsTo('App\Models\EventTemplate', 'event_template_id');
    }
    public function event_users(){
      return $this->hasMany('App\Models\EventUser');

    }
    public function lesson_requests(){
      return $this->hasMany('App\Models\LessonRequest');
    }
    public function scopeFindUser($query, $user_id)
    {
      if(empty($user_id)) return $query;
      return $query->whereHas('event_users', function($query) use ($user_id) {
          $query = $query->where('user_id', $user_id);
      });
    }
    //生徒あてのイベント条件
    public function scopeForStudent($query)
    {
      return $query->whereHas('template', function($query){
        $query->where('url', '!=', '');
        $query->whereHas('tags', function($query){
          $query->where('tag_key', 'user_role')->where('tag_value', 'student');
        });
      });
    }
    //講師あてのイベント条件
    public function scopeForTeacher($query)
    {
      return $query->whereHas('template', function($query){
        $query->where('url', '!=', '');
        $query->whereHas('tags', function($query){
          $query->where('tag_key', 'user_role')->where('tag_value', 'teacher');
        });
      });
    }
    public function getResponseTermAttribute(){
      return $this->term_format($this->response_from_date, $this->response_to_date, 'Y年m月d日');
    }
    public function getEventTermAttribute(){
      return $this->term_format($this->event_from_date, $this->event_to_date, 'Y年m月d日');
    }
    public function getEventUserCountAttribute(){
      return count($this->event_users);
    }
    public function getLessonRequestCountAttribute(){
      return count($this->lesson_requests);
    }
    public function getTemplateTitleAttribute(){
      return $this->template->name;
    }
    //本モデルはcreateではなくaddを使う
    static protected function add($form){
      $event = Event::create([
        'title' => $form['title'],
        'event_template_id' => $form['event_template_id'],
        'event_from_date' => $form['event_from_date'],
        'event_to_date' => $form['event_to_date'],
        'response_from_date' => $form['response_from_date'],
        'response_to_date' => $form['response_to_date'],
        'body' => $form['body'],
        'status' => 'new',
        'create_user_id' => $form['create_user_id'],
      ]);

      $event->change($form);
      $event->event_user_add();
      return $event->api_response(200, "", "", $event);
    }
    //本モデルはdeleteではなくdisposeを使う
    public function dispose(){
      EventUser::where('event_id', $this->id)->delete();
      $this->delete();
    }
    //本モデルはupdateではなくchangeを使う
    public function change($form, $file=null, $is_file_delete = false){
      $update_fields = [
        'title',
        'event_from_date',
        'event_to_date',
        'response_from_date',
        'response_to_date',
        'body',
      ];
      foreach($update_fields as $field){
        if(!isset($form[$field])) continue;
        $data[$field] = $form[$field];
      }
      $this->update($data);

      return $this;
    }
    public function getStatusNameAttribute(){
      $this->status_update();
      $status_name = "";
      if(app()->getLocale()=='en') return $this->status;

      if(isset(config('attribute.event_status')[$this->status])){
        $status_name = config('attribute.event_status')[$this->status];
      }
      return $status_name;
    }
    public function status_update(){
      if($this->status=='cancel' || $this->status=='closed') return;
      $status = $this->status;
      if(strtotime(date('now')) > strtotime($this->event_from_date)){
        //開催日（開始）経過
        $status = 'progress';
      }
      if(strtotime(date('now')) > strtotime($this->event_to_date)){
        //開催日（終了）経過
        $status = 'closed';
      }
      if($this->status!=$status){
        $this->update(['status'=>$status]);
      }
    }
    public function get_event_user(){
      $user_role_tag = $this->template->get_tag('user_role');
      if(!isset($user_role_tag)) return null;
      $target = null;
      switch($user_role_tag->tag_value){
        case "student":
          $target = Student::query();
          break;
        case "teacher":
          $target = Teacher::query();
          break;
        case "manager":
          $target = Manager::searchTags([['tag_key' => 'manager_type', 'tag_value'=>'admin']]);
          break;
        case "stuff":
          $target = Manager::query();
          break;
        case "parent":
          $target = StudentParent::query();
          break;
      }
      if($target==null) return null;
      $targets = $target->findStatuses(['regular'])->where('user_id', '!=', 1);
      $lesson_tag = $this->template->get_tags('lesson');
      if(isset($lesson_tag) && count($lesson_tag)>0){
        $targets = $targets->searchTags($lesson_tag);
      }
      $grade_tags = $this->template->get_tags('grade');
      if(isset($grade_tags) && count($grade_tags)>0){
        $targets = $targets->searchTags($grade_tags);
      }
      $targets = $targets->get();
      return $targets;
    }
    public function event_user_add(){
      $targets = $this->get_event_user();
      foreach($targets as $target){
        $eu = EventUser::where('event_id', $this->id)->where('user_id' , $target->user_id)->first();
        if(isset($eu)) continue;
        EventUser::create([
          'event_id' => $this->id,
          'user_id' => $target->user_id,
          'status' => 'new',
        ]);
      }
    }
    public function has_user($user_id){
      $u = $this->event_users->where('user_id', $user_id)->first();
      if(isset($u)) return true;
      return false;
    }
    public function get_event_dates(){
      $event_dates = [];
      $d = $this->event_from_date;
      while(strtotime($this->event_to_date) >= strtotime($d)){
        $h = Holiday::where('date', $d)->first();
        if(!isset($h) || $h->is_private_holiday()==false){
          $event_dates[] = $d;
        }
        $d = date('Y-m-d', strtotime('+1 day '.$d));
      }
      return $event_dates;
    }
    public function is_answerable(){
      if(strtotime($this->response_from_date) < strtotime('now') && strtotime($this->response_to_date) > strtotime('now')){
        return true;
      }
      return false;
    }
    public function is_need_request(){
      //暫定対応＝templateのurlがある場合、lesson_requestを必要とする
      if(!empty($this->template->url)) return true;
      return false;
    }
    public function _get_calendar($filter){
      if($this->is_need_request()!=true) return [];
      $lesson_request_dates = LessonRequestDate::searchEvent($this->id);
      if(isset($filter['lesson_request_id'])){
        $lesson_request_dates = $lesson_request_dates->where('lesson_request_id', $filter['lesson_request_id']);
      }
      $data = LessonRequestCalendar::whereIn('lesson_request_date_id', $lesson_request_dates->pluck('id'));

      if(isset($filter['search_status'])){
        $data = $data->findStatuses($filter['search_status']);
      }
      if(isset($filter['search_status'])){
        $data = $data->findStatuses($filter['search_status']);
      }

      return $data;
    }
    public function get_calendar($filter){
      if($this->is_need_request()!=true) return [];
      return $this->_get_calendar($filter)->orderBy('start_time')->orderBy('place_floor_id')->get();
    }
    public function get_calendar_count($filter){
      if($this->is_need_request()!=true) return 0;
      return $this->_get_calendar($filter)->count();
    }
    public function add_matching_calendar($selected_lesson_request_ids){
      if($selected_lesson_request_ids==null) return false;
      if($this->is_need_request()!=true) return false;
      //if($this->status != 'new') return false;
      //このイベントに対する申し込みを取得
      $ids = LessonRequestDate::searchEvent($this->id)->whereIn('lesson_request_id', $selected_lesson_request_ids)->pluck('id')->toArray();
      LessonRequestCalendar::whereIn('lesson_request_date_id', $ids)->delete();
      $this->_add_matching_calendar($selected_lesson_request_ids);
    }
    public function _add_matching_calendar($selected_lesson_request_ids){
      echo "<h4>--------------_add_matching_calendar()------------------------</h4>";
      foreach($this->lesson_requests->whereIn('id', $selected_lesson_request_ids) as $r){
        foreach($r->get_tags('lesson_place') as $tag){
          $r->_add_matching_calendar_for_place($tag->tag_value);
          break;
        }
      }
      echo "<h4>--------------_add_matching_calendar_for_date end------------------------</h4>";
    }
    public function is_season_lesson(){
      //TODO 属性が不足しているので、LessonRequestのタイプで識別する
      //暫定対応：季節講習かどうか
      if(!isset($this->lesson_requests) || count($this->lesson_requests) < 1) return false;
      if($this->lesson_requests[0]->type=='season_lesson') return true;
      return false;
    }
}
