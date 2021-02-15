<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StudentGroupMember
 *
 * @property int $id
 * @property int $student_group_id 生徒グループID
 * @property int $student_id 生徒ID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\StudentGroup $student_group
 * @method static \Illuminate\Database\Eloquent\Builder|StudentGroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentGroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentGroupMember query()
 * @mixin \Eloquent
 */
class StudentGroupMember extends Model
{
  protected $table = 'common.student_group_members';
  protected $guarded = array('id');
  public static $rules = array(
    'student_group_id' => 'required',
    'student_id' => 'required',
  );
  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  public function student_group(){
    return $this->belongsTo('App\Models\StudentGroup', 'student_group_id');
  }
  public function is_family(){
    $relations = StudentRelation::where('student_id', $this->student_id)->get();
    foreach($relations as $relation){
    }
  }
}
