<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextMaterial
 *
 * @property int $id
 * @property string $name 保存ファイル名
 * @property string|null $description 説明
 * @property string $type mimetype
 * @property int $size ファイルサイズ
 * @property string $s3_url S3ダウンロードURL
 * @property string $publiced_at 公開日
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $importance_label
 * @property-read mixed $publiced_date
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|TextMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
class TextMaterial extends Milestone
{
  //リンクするテーブル名
  protected $table = 'lms.text_materials';
  //編集不能とするフィールド
  protected $guarded = array('id');
  //登録時に入力必須のフィールド
  public static $rules = array(
      'name' => 'required',
  );

  protected $appends = ['create_user_name', 'publiced_date', 'created_date', 'updated_date'];

  public function create_user(){
    return $this->belongsTo('App\User', 'create_user_id');
  }
  public function shared_users(){
    return $this->morphToMany('App\User', 'shared_userable');
  }
  public function curriculums(){
    return $this->morphToMany('App\Models\Curriculum', 'curriculumable');
  }
  public function getPublicedDateAttribute(){
    return $this->_date_label($this->publiced_at, 'Y年m月d日');
  }
  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name','like', $_like)
          ->orWhere('description','like', $_like);
      }
    });
    return $query;
  }
  public function scopeSearchCurriculums($query, $curriculums){
    if(!isset($curriculums)) return $query;
    if(!isset($this->curriculums)) return $query;
    return $query->whereHas('curriculums', function($query) use ($curriculums) {
      $query->whereIn('curriculum_id', $curriculums);
    });
  }
}
