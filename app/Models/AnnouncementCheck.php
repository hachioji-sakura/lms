<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementCheck extends CommentCheck
{
  protected $table = 'lms.announcement_checks';
  protected $guarded = array('id');
  public static $rules = array(
      'announcement_id' => 'required',
      'check_user_id' => 'required',
  );

  public function announcement(){
    return $this->belongsTo('App\Models\Announcement', 'announcement_id');
  }
}
