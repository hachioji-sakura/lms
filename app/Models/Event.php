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
      $event->evet_user_add();
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
      $status_name = "";
      if(app()->getLocale()=='en') return $this->status;

      if(isset(config('attribute.event_status')[$this->status])){
        $status_name = config('attribute.event_status')[$this->status];
      }
      return $status_name;
    }
    public function evet_user_add(){
      $user_ids = [];
      $lesson_tag = $this->template->get_tag('lesson');
      $user_role_tag = $this->template->get_tag('user_role');
      $grade_tags = $this->template->get_tags('grade');
      if(!isset($lesson_tag)) return ;
      if(!isset($user_role_tag)) return ;
      $target = null;
      switch($user_role_tag->tag_value){
        case "student":
          $target = Student::query();
          break;
        case "teacher":
          $target = Teacher::query();
          break;
        case "manager":
          $target = Manager::query();
          break;
        case "parent":
          $target = StudentParent::query();
          break;
      }
      if($target==null) return;
      $targets = $target->findStatuses(['regular'])->searchTags([$lesson_tag]);
      if(isset($grade_tags) && count($grade_tags)>0){
        $targets = $targets->searchTags($grade_tags);
      }
      $targets = $targets->get();
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
}
