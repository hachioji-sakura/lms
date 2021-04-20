<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
/**
 * App\Models\Event
 *
 * @property int $id
 * @property int $event_template_id イベントテンプレートID
 * @property string $title 件名
 * @property string $event_from_date 開催期間_始
 * @property string $event_to_date 開催期間_終
 * @property string $response_from_date 回答期間_始
 * @property string $response_to_date 回答期間_終
 * @property string|null $body 内容
 * @property string $status ステータス
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|EventUser[] $event_users
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $event_term
 * @property-read mixed $event_user_count
 * @property-read mixed $importance_label
 * @property-read mixed $response_term
 * @property-read mixed $status_name
 * @property-read mixed $target_user_name
 * @property-read mixed $template_title
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\EventTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
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
      if(strtotime(date('Y-m-d H:i:s')) > strtotime($this->event_from_date." 00:00:00")){
        //開催日（開始）経過
        $status = 'progress';
      }
      if(strtotime(date('Y-m-d H:i:s')) > strtotime($this->event_to_date." 00:00:00")){
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
