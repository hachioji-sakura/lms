<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextMaterial extends Model
{
  //リンクするテーブル名
  protected $table = 'lms.text_materials';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'name' => 'required',
  );
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
