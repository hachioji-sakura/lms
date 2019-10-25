<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AskComment extends Milestone
{
  protected $connection = 'mysql';
  protected $table = 'lms.ask_comments';
  protected $guarded = array('id');

  public static $rules = array(
    'ask_id' => 'required',
    'body' => 'required',
    'create_user_id' => 'required',
  );
  public function ask(){
    return $this->belongsTo('App\Models\Ask', 'ask_id');
  }
  public function details(){
    $item = $this;
    $item["created_date"] = $this->created_at_label();
    $item["updated_date"] = $this->updated_at_label();
    $item["create_user_name"] = $this->create_user->details()->name();
    return $item;
  }

}
