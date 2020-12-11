<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;
use App\Models\Traits\WebCache;
use App\Models\Traits\Scopes;

class TuitionMaster extends Model
{
    //
    use WebCache;
    use Scopes;
    use Common;

    protected $table = 'common.tuition_masters';
    protected $guarded = array('id');
    protected $fillable = [
      'grade',
      'fee',
      'lesson',
      'course_type',
      'course_minutes',
      'lesson_week_count',
      'is_exam',
      'subject',
      'title',
      'remark',
      'start_date',
      'end_date',
    ];

    protected $attributes = [
      'create_user_id' => 1,
    ];
}
