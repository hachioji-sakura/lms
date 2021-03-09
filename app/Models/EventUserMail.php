<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventUser;

/**
 * App\Models\EventUserMail
 *
 * @property int $id
 * @property int $event_user_id イベントユーザーID
 * @property int $mail_id メールID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read EventUser $event_user
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $importance_label
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\Models\MailLog $mail
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EventUserMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventUserMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|EventUserMail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
class EventUserMail extends Milestone
{
  protected $table = 'lms.event_user_mails'; //テーブル名を入力
  protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

  public static $rules = array( //必須フィールド
      'user_id' => 'required',
      'event_id' => 'required',
  );
  protected $appends = ['created_date', 'updated_date'];

  public function mail(){
    return $this->belongsTo('App\Models\MailLog', 'mail_id');
  }
  public function event_user(){
    return $this->belongsTo('App\Models\EventUser', 'event_user_id');
  }

}
