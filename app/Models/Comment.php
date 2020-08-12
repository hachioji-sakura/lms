<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommentCheck;

class Comment extends Milestone
{
  protected $table = 'lms.comments';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  protected $appends = ['type_name', 'create_user_name', 'target_user_name', 'publiced_date', 'created_date', 'updated_date'];

  public function getTypeNameAttribute(){
    $ret = $this->attribute_name('comment_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }

  public function type_name()
  {
    $ret = $this->attribute_name('comment_type', $this->type);
    if(!empty($ret)) return $ret;
    $ret =  __('labels.'.$this->type.'_comment');
    if(!empty($ret)) return $ret;
    return "";
  }
  public function publiced_date(){
    return $this->_date_label($this->publiced_at, 'Y年m月d日');
  }
  public function getPublicedDateAttribute(){
    return $this->_date_label($this->publiced_at);
  }

  public function scopeFindDefaultTypes($query, $domain)
  {
    $_types = config('attribute.comment_type');
    $types = [];
    foreach($_types as $index => $val){
      $types[] = $index;
    }
    $types[] = 'trial';
    $types[] = 'entry';
    return $this->scopeFindTypes($query, $types);
  }
  public function scopeChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id in (select comment_id from lms.comment_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }
  public function scopeUnChecked($query, $user_id)
  {
    $where_raw = <<<EOT
      id not in (select comment_id from lms.comment_checks where check_user_id = ? and is_checked=1)
EOT;
    return $query->whereRaw($where_raw,[$user_id]);
  }

  public function comment_checks(){
    return $this->hasMany('App\Models\CommentCheck');
  }
  public function is_check($user_id){
    $check = CommentCheck::where('comment_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)) return false;
    return $check->is_checked;
  }
  public function check($user_id, $val=1){
    $check = CommentCheck::where('comment_id', $this->id)->where('check_user_id', $user_id)->first();
    if(!isset($check)){
      $check = CommentCheck::create([
        'comment_id' => $this->id,
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
