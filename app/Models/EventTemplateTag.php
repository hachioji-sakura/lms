<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EventTemplateTag
 *
 * @property int $id
 * @property int $event_template_id イベントテンプレートID
 * @property string $tag_key タグキー
 * @property string $tag_value タグ値
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EventTemplate $event_template
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findKey($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag findUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateTag query()
 * @mixin \Eloquent
 */
class EventTemplateTag extends UserTag
{

  protected $table = 'lms.event_template_tags';
  protected $guarded = array('id');
  public static $id_name = 'event_template_id';

  public static $rules = array(
      'event_template_id' => 'required',
      'tag_key' => 'required',
      'tag_value' => 'required'
  );
  public function event_template(){
    return $this->belongsTo('App\Models\EventTemplate', 'event_template_id');
  }

}
