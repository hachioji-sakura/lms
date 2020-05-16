<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Comment
{
    //
    protected $table = 'lms.messages';
    protected $guarded = array('id');

    public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'target_user' => 'required',
      'create_user' => 'required',
    );

    public function scopeFindRootMessage($query,$parent_message_id){
      return $query->where('parent_message_id',0)
                  ->where('id', $parent_message_id);
    }

    public function scopeFindMyMessage($query, $id){
      return $query->where('target_user_id', $id)
                   ->orWhere('create_user_id',$id);
    }

}
