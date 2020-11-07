<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;
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


    //本モデルはcreateではなくaddを使う
    static protected function add($form){
      $ret = [];
      $event_template = EventTemplate::create([
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
      EventUser
      $event_template->change($form);

      return $event_template->api_response(200, "", "", $event_template);
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




}
