<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
class Event extends Milestone
{
    //
    protected $table = 'lms.events'; //テーブル名を入力
    protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)
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
    public function getResponseTermAttribute(){
      return $this->term_format($this->response_from_date, $this->response_to_date, 'Y年m月d日');
    }
    public function getEventTermAttribute(){
      return $this->term_format($this->event_from_date, $this->event_to_date, 'Y年m月d日');
    }
    public function getEventUserCountAttribute(){
      return count($this->event_users);
    }
    public function getTemplateTitleAttribute(){
      return $this->template->name;
    }
    //本モデルはcreateではなくaddを使う
    static protected function add($form){
      $ret = [];
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
}
