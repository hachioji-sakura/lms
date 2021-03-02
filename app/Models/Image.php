<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property string $name 保存ファイル名
 * @property string $type mimetype
 * @property int $size ファイルサイズ
 * @property string $s3_url S3ダウンロードURL
 * @property string $alias エイリアス
 * @property string $publiced_at 公開日
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Image findCreateUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image publiced()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @mixin \Eloquent
 */
class Image extends Model
{
  protected $table = 'lms.images';
  protected $guarded = array('id');

  public static $rules = array(
      'name' => 'required'
  );

  public function scopeFindCreateUser($query, $val){
    return $query->where('create_user_id', $val);
  }
  public function scopePubliced($query){
    return $query->orWhere('publiced_at','<=', date('Y-m-d'));
  }
}
