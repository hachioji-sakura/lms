<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextMaterial extends Milestone
{
  //リンクするテーブル名
  protected $table = 'lms.text_materials';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'name' => 'required',
  );

  protected $appends = ['create_user_name', 'publiced_date', 'created_date', 'updated_date'];

  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function getPublicedDateAttribute(){
    return $this->_date_label($this->publiced_at);
  }

}
