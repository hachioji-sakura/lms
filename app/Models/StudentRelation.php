<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StudentRelation
 *
 * @property int $id
 * @property int $student_id 生徒ID
 * @property int $student_parent_id 保護者ID
 * @property string $type 関係性
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StudentParent $parent
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation findParent($val)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation findParents($val)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation findStudent($val)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation likeStudentName($search_word)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentRelation query()
 * @mixin \Eloquent
 */
class StudentRelation extends Model
{
  protected $table = 'common.student_relations';
  protected $guarded = array('id');

  public static $rules = array(
      'student_id' => 'required',
      'student_parent_id' => 'required',
  );
  public function student(){
    return $this->belongsTo('App\Models\Student', 'student_id');
  }
  public function parent(){
    return $this->belongsTo('App\Models\StudentParent', 'student_parent_id');
  }
  public function scopeLikeStudentName($query, $search_word)
  {
    if(empty($search_word)) return $query;
    $where_raw = '';
    $words = explode(' ', $search_word);
    foreach($words as $word){
      if(empty($word)) continue;
      $_like = "'%".$word."%'";
      $where_raw .= 'OR name_last like '.$_like;
      $where_raw .= 'OR name_first like '.$_like;
      $where_raw .= 'OR kana_last like '.$_like;
      $where_raw .= 'OR kana_first like '.$_like;
    }
    $where_raw = $this->table.'.student_id in (select id from students where '.trim($where_raw, 'OR ').')';
    return $query->whereRaw($where_raw,[]);
  }
  public function scopeFindParent($query, $val)
  {
      return $query->where('student_parent_id', $val);
  }
  public function scopeFindParents($query, $val)
  {
      return $query->whereIn('student_parent_id', $val);
  }
  public function scopeFindStudent($query, $val)
  {
      return $query->where('student_id', $val);
  }
  public function current_calendar()
  {
    $student = Student::where('id', $this->student_id)->first();
    $calendar = UserCalendar::findUser($student->user_id)
      ->rangeDate(date('Y-m-d 00:00:00'))
      ->orderBy('start_time')->first();
    return $calendar;
  }

}
