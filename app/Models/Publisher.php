<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Publisher
 *
 * @property int $id
 * @property string $name
 * @property string $url 出版社HP
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Textbook[] $textbook
 * @method static \Illuminate\Database\Eloquent\Builder|Publisher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Publisher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Publisher query()
 * @mixin \Eloquent
 */
class Publisher extends Model
{
  protected $table = 'lms.publishers';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function textbook(){
    return $this->hasMany('App\Models\Textbook','id', 'publisher_id');
  }
}
