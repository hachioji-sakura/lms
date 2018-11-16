<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
  protected $table = 'comments';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    switch($this->type){
      case "study":
        return "学習内容";
        break;
    }
    return "その他";
  }
  public function target_user(){
    return $this->belongsTo('App\User', 'target_user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
