<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnouncementCheck
 *
 * @property int $id
 * @property int $announcement_id 事務コメントID
 * @property int $check_user_id チェックしたユーザー
 * @property int $is_checked チェックした=1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Announcement $announcement
 * @property-read \App\User $check_user
 * @property-read \App\Models\Comment $comment
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementCheck query()
 * @mixin \Eloquent
 */
class AnnouncementCheck extends CommentCheck
{
  protected $table = 'lms.announcement_checks';
  protected $guarded = array('id');
  public static $rules = array(
      'announcement_id' => 'required',
      'check_user_id' => 'required',
  );

  public function announcement(){
    return $this->belongsTo('App\Models\Announcement', 'announcement_id');
  }
}
