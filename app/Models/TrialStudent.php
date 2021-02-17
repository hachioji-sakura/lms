<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrialStudent
 *
 * @property int $id
 * @property int $trial_id 体験申し込みID
 * @property int $student_id 対象生徒ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\Trial $trial
 * @method static \Illuminate\Database\Eloquent\Builder|TrialStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialStudent query()
 * @mixin \Eloquent
 */
class TrialStudent extends Model
{
  protected $table = 'lms.trial_students';
  protected $guarded = array('id');
  public static $rules = array(
      'user_id' => 'required',
  );
  public function trial(){
    return $this->belongsTo('App\Models\Trial', 'trial_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
}
