<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Milestone
{
  protected $table = 'lms.comments';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
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
  public function details(){
    $item =  parent::details();
    $item["publiced_date"] = $this->publiced_date();
    return $item;
  }
  public function scopeFindDefaultTypes($query)
  {
    $_types = config('attribute.comment_type');
    $types = [];
    foreach($_types as $index => $val){
      $types[] = $index;
    }
    return $this->scopeFindTypes($query, $types);
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
