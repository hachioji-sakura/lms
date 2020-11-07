<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Traits\Common;

class EventTemplate extends Milestone
{
    use Common;
    protected $table = 'lms.event_templates'; //テーブル名を入力(A5MK参照)
    protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)
    public static $rules = array( //必須フィールド
        'role' => 'required',
        'event_name' => 'required'
    );
    public function tags(){
      return $this->hasMany('App\Models\EventTemplateTag', 'event_template_id');
    }
    //本モデルはcreateではなくaddを使う
    static protected function add($form){

      $ret = [];
      $event_template = EventTemplate::create([
        'name' => $form['name'],
        'remark' => $form['remark'],
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
        'name',
        'remark',
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
