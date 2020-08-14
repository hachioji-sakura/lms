<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralAttribute;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\Traits\Common;

class Milestone extends Model
{
  use Common;
  protected $connection = 'mysql';
  protected $table = 'lms.milestones';
  protected $guarded = array('id');
  protected $appends = ['type_name', 'create_user_name', 'target_user_name', 'importance_label', 'created_date', 'updated_date'];

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    return $this->attribute_name('milestone_type', $this->type);
  }
  public function scopeRangeDate($query, $from_date, $to_date=null)
  {
    $field = 'created_at';
    //日付検索
    if($from_date == $to_date){
      $query = $query->where(DB::raw('cast(created_at as date)'), $from_date);
    }
    else {
      if(!empty($from_date)){
        $query = $query->where($field, '>=', $from_date);
      }
      if(!empty($to_date)){
        $query = $query->where($field, '<', $to_date);
      }
    }
    return $query;
  }
  public function scopeFindTargetUser($query, $val)
  {
      return $query->where('target_user_id', $val);
  }
  public function scopeStatus($query, $val)
  {
      return $query->where('status', $val);
  }
  public function scopeFindTypes($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'type', $vals, $is_not);
  }
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }
  public function scopeSearchWord($query, $word){
    $search_words = explode(' ', urlencode($word));
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('body','like', $_like)
          ->orWhere('title','like', $_like);
      }
    });
    return $query;
  }
  public function scopeSortCreatedAt($query, $sort){
    if(empty($sort)) $sort = 'asc';
    return $query->orderBy('created_at', $sort);
  }
  public function scopePagenation($query, $page, $line){
    $_line = $this->pagenation_line;
    if(is_numeric($line)){
      $_line = $line;
    }
    $_page = 0;
    if(is_numeric($page)){
      $_page = $page;
    }
    $_offset = $_page*$_line;
    if($_offset < 0) $_offset = 0;
    return $query->offset($_offset)->limit($_line);
  }
  public function target_user(){
    return $this->belongsTo('App\User', 'target_user_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function importance_name(){
    $res = $this->attribute_name('importance', $this->importance);
    return $res;
  }
  public function getTypeNameAttribute(){
    return $this->attribute_name('milestone_type', $this->type);
  }
  public function getImpotanceLabelAttribute(){
    $res = $this->attribute_name('importance', $this->importance);
    return $res;
  }
  public function getCreateUserNameAttribute(){
    if(!isset($this->create_user)) return "";
    return $this->create_user->details()->name();
  }
  public function getTargetUserNameAttribute(){
    if(!isset($this->target_user)) return "";
    return $this->target_user->details()->name();
  }

  public function send_mail($user_id, $title, $param, $type, $template){
    $u = User::where('id', $user_id)->first();
    $mail = $u->get_mail_address();
    if(!isset($u)) return $controller->bad_request();
    $param['user'] = $u->details();
    $param['send_to'] = $param['user']->role;
    $res = $this->_send_mail($mail, $title, $param, $type, $template, $u->locale);
    return $res;
  }
  public function default_importance($type){
    switch($type){
      case 'trial':
        return 10;
        break;
      case 'entry':
        return 10;
        break;
      case 'importance':
        return 10;
        break;
      case 'season':
        return 5;
        break;
    }
    return 0;
  }
  public function change($form, $file=null, $is_file_delete = false){
    $s3_url = '';
    $_form = $this->file_upload($file);
    $form['s3_url'] = $_form['s3_url'];
    $form['s3_alias'] = $_form['s3_alias'];
    if($is_file_delete == true){
      $form['s3_url'] = "";
      $form['s3_alias'] = "";
    }
    if($is_file_delete==true){
      //削除指示がある、もしくは、更新する場合、S3から削除
      $this->s3_delete($this->s3_url);
    }
    $this->update($form);
    return $this;
  }
  public function file_upload($file=null){
    $form = ['s3_alias' => $this->s3_alias, 's3_url' => $this->s3_url];

    if(!empty($file)){
      if ($file->isValid([])) {
        $s3 = $this->s3_upload($file, config('aws_s3.upload_folder'));
        if(!empty($this->s3_url)){
          $this->s3_delete($this->s3_url);
        }
        $form['s3_alias'] = $file->getClientOriginalName();
        $form['s3_url'] = $s3['url'];
        $this->update($form);
      }
    }
    return $form;
  }
  public function dispose(){
    if(isset($this->s3_url) && !empty($this->s3_url)){
      //S3アップロードファイルがある場合は削除
      $this->s3_delete($this->s3_url);
    }
    $this->delete();
  }

  public function tasks(){
    return $this->hasMany('App\Models\Task', 'milestone_id', 'id');
  }
}
