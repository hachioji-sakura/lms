<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChargeStudentTag
 *
 * @property int $id
 * @property int $charge_student_id 担当生徒ID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChargeStudent $calendar
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag findChargeStudent($val)
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChargeStudentTag query()
 * @mixin \Eloquent
 */
class ChargeStudentTag extends UserTag
{
  protected $connection = 'mysql';
  protected $table = 'lms.charge_student_tags';
  public static $id_name = 'charge_student_id';
  protected $guarded = array('id');
  public static $rules = array(
      'charge_student_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function calendar(){
    return $this->belongsTo('App\Models\ChargeStudent', 'charge_student_id');
  }
  public function scopeFindChargeStudent($query, $val)
  {
      return $query->where('charge_student_id', $val);
  }
}
