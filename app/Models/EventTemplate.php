<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Traits\Common;

/**
 * App\Models\EventTemplate
 *
 * @property int $id
 * @property string $title 件名
 * @property string|null $url URL
 * @property string|null $body 内容
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $grade
 * @property-read mixed $importance_label
 * @property-read mixed $lesson
 * @property-read mixed $role
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventTemplateTag[] $tags
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
class EventTemplate extends Milestone
{
    use Common;
    protected $table = 'lms.event_templates'; //テーブル名を入力(A5MK参照)
    protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)
    public static $rules = array( //必須フィールド
        'title' => 'required'
    );
    protected $appends = ['role', 'lesson', 'grade', 'created_date', 'updated_date'];

    public function tags(){
      return $this->hasMany('App\Models\EventTemplateTag', 'event_template_id');
    }
    //本モデルはcreateではなくaddを使う
    static protected function add($form){

      $ret = [];
      $event_template = EventTemplate::create([
        'title' => $form['title'],
        'create_user_id' => $form['create_user_id'],
      ]);
      $event_template->change($form);

      return $event_template->api_response(200, "", "", $event_template);
    }
    //本モデルはdeleteではなくdisposeを使う
    public function dispose(){
      EventTemplateTag::where('event_template_id', $this->id)->delete();
      $this->delete();
    }
    //本モデルはupdateではなくchangeを使う
    public function change($form, $file=null, $is_file_delete = false){
      $update_fields = [
        'title',
        'body',
        'url',
      ];
      foreach($update_fields as $field){
        if(!isset($form[$field])) continue;
        $data[$field] = $form[$field];
      }
      $this->update($data);

      //タグ
      $tag_names = ['lesson', 'grade', 'user_role'];
      foreach($tag_names as $tag_name){
        if(!empty($form[$tag_name])){
          EventTemplateTag::setTags($this->id, $tag_name, $form[$tag_name], $form['create_user_id']);
        }
      }
      return $this;
    }
    public function getRoleAttribute(){
      return $this->get_tags_name('user_role');
    }
    public function getLessonAttribute(){
      return $this->get_tags_name('lesson');
    }
    public function getGradeAttribute(){
      return $this->get_tags_name('grade');
    }

}
