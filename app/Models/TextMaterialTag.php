<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextMaterialTag extends Model
{
  //リンクするテーブル名
  protected $table = 'lms.text_material_tags';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'text_material_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function text_material(){
    return $this->belongsTo('App\TextMaterial');
  }
}
