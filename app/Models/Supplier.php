<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Supplier
 *
 * @property int $id
 * @property string $name
 * @property string $url 販売会社HP
 * @property int $publisher_id 出版社ID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Textbook[] $textbook
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @mixin \Eloquent
 */
class Supplier extends Model
{
  protected $table = 'lms.suppliers';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function textbook(){
    return $this->hasMany('App\Models\Textbook');
  }
}
