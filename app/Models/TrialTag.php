<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrialTag
 *
 * @property int $id
 * @property int $trial_id 体験申し込みID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Trial $trial
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialTag query()
 * @mixin \Eloquent
 */
class TrialTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.trial_tags';
  public static $id_name = 'trial_id';
  protected $guarded = array('id');
  public static $rules = array(
      'trial_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function trial(){
    return $this->belongsTo('App\Models\Trial', 'trial_id');
  }



}
