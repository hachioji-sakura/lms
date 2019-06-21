<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
  protected $table = 'lms.images';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required'
  );

  public function scopeFindCreateUser($query, $val){
    return $query->where('create_user_id', $val);
  }
  public function scopePubliced($query){
    return $query->orWhere('publiced_at','<=', date('Y-m-d'));
  }
}
