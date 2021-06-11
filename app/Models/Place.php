<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

/**
 * App\Models\Place
 *
 * @property int $id
 * @property string $name 所在地名
 * @property string|null $name_en 名称（英語）
 * @property int $sort_no 表示順
 * @property string|null $post_no 郵便番号
 * @property string|null $address 住所
 * @property string|null $phone_no 連絡先
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlaceFloor[] $floors
 * @property-read mixed $created_date
 * @property-read mixed $is_use
 * @property-read mixed $updated_date
 * @method static \Illuminate\Database\Eloquent\Builder|Place fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Place findId($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Place newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Place newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Place query()
 * @method static \Illuminate\Database\Eloquent\Builder|Place searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Place searchWord($word)
 * @mixin \Eloquent
 */
class Place extends Model
{
  use Common;
  protected $table = 'common.places';
  protected $guarded = array('id');
  public static $rules = array(
      'name' => 'required',
  );
  public function scopeFindId($query, $val)
  {
      return $query->where('id', $val);
  }
  /**
   *　リレーション：フロア
   */
  public function floors(){
    return $this->hasMany('App\Models\PlaceFloor', 'place_id');
  }
  public function name(){
    if(app()->getLocale()=='en') return $this->name_en;
    return $this->name;
  }
  public function is_use(){
    foreach($this->floors as $floor){
      if($floor->is_use==true) return true;
    }
    return false;
  }
  //自宅という文字を含むかどうか
  //自宅の場合は地図表示不要などに利用する
  public function is_home(){
    if(strpos($this->name, '自宅')!==false) return true;
    return false;
  }
  public function getIsUseAttribute(){
    if($this->is_use()==true) return '使用中';
    return '未使用';
  }

  public function getStatusNameAttribute(){
    return config('attribute.place_status')[$this->status];
  }

  public function scopeSearchWord($query, $word){
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name','like', $_like)
          ->orWhere('address','like', $_like)
          ->orWhere('name_en','like', $_like);
      }
    });
    return $query;
  }

  public function scopeEnable($query){
    return $query->where('status','enabled');
  }

  public function dispose(){
    if($this->is_use()==true){
      return $this->error_response('このデータはカレンダーにて使用されており削除できません');
    }
    foreach($this->floors as $floor){
      PlaceFloorSheat::where('place_floor_id', $floor->id)->delete();
    }
    PlaceFloor::where('place_id', $this->id)->delete();
    $this->delete();
  }

  public function scopeHasPhoneNo($query){
    return $query->where('phone_no','<>','');
  }
}
