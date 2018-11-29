<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Milestone
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
    $types = config('attribute.comment_type');
    return $types[$this->type];
  }
  /*
  public function scopeStatus($query, $val)
  {
      return $query->where('status', $val);
  }
  public function target_user(){
    return $this->belongsTo('App\User', 'target_user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  */
}
