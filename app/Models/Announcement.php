<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Comment
{
  protected $table = 'lms.announcements';
  protected $guarded = array('id');

  public static $rules = array(
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    $ret = $this->attribute_name('announcement_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }
  public function scopeFindDefaultTypes($query, $domain)
  {
    $_types = config('attribute.announcement_type');
    $types = [];
    foreach($_types as $index => $val){
      $types[] = $index;
    }
    return $this->scopeFindTypes($query, $types);
  }
  public function scopeUnChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id not in (select comment_id from lms.announcement_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function comment_checks(){
    return $this->hasMany('App\Models\AnnouncementCheck');
  }
  public function is_check($user_id){
    $check = AnnouncementCheck::where('announcement_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)) return false;
    return $check->is_checked;
  }
  public function check($user_id, $val=1){
    $check = AnnouncementCheck::where('announcement_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)){
      $check = AnnouncementCheck::create([
        'announcement_id' => $this->id,
        'check_user_id' => $user_id,
        'is_checked' => $val
      ]);
    }
    else {
      $check->update(['is_checked' => $val]);
    }
    return $check;
  }
  public function uncheck($user_id){
    return $this->check($user_id, 0);
  }
}
