<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentCheck extends Model
{
  protected $table = 'lms.comment_checks';
  protected $guarded = array('id');
  public static $rules = array(
      'comment_id' => 'required',
      'check_user_id' => 'required',
  );

  public function check_user(){
    return $this->belongsTo('App\User', 'check_user_id');
  }
  public function comment(){
    return $this->belongsTo('App\Models\Comment', 'comment_id');
  }

}
