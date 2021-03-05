<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserTextbook
 *
 * @property int $id
 * @property int $user_id 所有者ID
 * @property int $textbook_id 所有テキストＩＤ
 * @property int $status 所有状況
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Textbook $textbook
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTextbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTextbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTextbook query()
 * @mixin \Eloquent
 */
class UserTextbook extends Model
{
  protected $table = 'lms.user_textbooks';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'user_id' => 'required',
  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook');
  }
  public function user(){
    return $this->hasOne('App\User');
  }
}
