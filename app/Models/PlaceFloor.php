<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserCalendar;
use App\Models\Traits\Common;

/**
 * App\Models\PlaceFloor
 *
 * @property int $id
 * @property int $place_id 所在地ID
 * @property string $name フロア名
 * @property string|null $name_en 名称（英語）
 * @property int $sort_no 表示順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $created_date
 * @property-read mixed $is_use
 * @property-read mixed $sheat_count
 * @property-read mixed $updated_date
 * @property-read \App\Models\Place $place
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlaceFloorSheat[] $sheats
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloor fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloor query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceFloor searchTags($tags)
 * @mixin \Eloquent
 */
class PlaceFloor extends Model
{
  use common;
  protected $table = 'common.place_floors';
  protected $guarded = array('id');

  public static $rules = array(
    'name' => 'required',
  );
  /**
   *　リレーション：所在地
   */
  public function place(){
    return $this->belongsTo('App\Models\Place', 'place_id');
  }
  /**
   *　リレーション：座席
   */
  public function sheats(){
    return $this->hasMany('App\Models\PlaceFloorSheat', 'place_floor_id');
  }
  public function getSheatCountAttribute(){
    if(isset($this->sheats)) return count($this->sheats);
    return 0;
  }
  public function name(){
    if(app()->getLocale()=='en') return $this->name_en;
    return $this->name;
  }
  public function is_use(){
    $c = UserCalendar::where('place_floor_id', $this->id)->count();

    if($c>0) return true;
    foreach($this->sheats as $sheat){
      if($sheat->is_use()==true) return true;
    }
    return false;
  }
  public function getIsUseAttribute(){
    if($this->is_use()==true) return '使用中';
    return '未使用';
  }
  public function dispose(){
    if($this->is_use()==true){
      return $this->error_response('このデータはカレンダーにて使用されており削除できません');
    }

    PlaceFloorSheat::where('place_floor_id', $this->id)->delete();
    $this->delete();
  }
  public function get_free_seat($start_time, $end_time){
    //指定した時間帯・フロアのカレンダーを取得(新規、キャンセル、休み、休講は利用扱いではない）
    $calendars = UserCalendar::searchDate($start_time, $end_time)
              ->whereNotIn('status', ['cancel', 'rest', 'lecture_cancel', 'new'])
              ->where('place_floor_id', $this->id)->get();
    foreach($this->sheats as $sheat){
      $is_use = false;
      foreach($calendars as $calendar){
        foreach($calendar->members as $member){
          if($member->is_active()==true && $member->place_floor_sheat_id == $sheat->id){
            //この座席は利用予定（予約済み）
            $is_use = true;
            break;
          }
        }
        if($is_use===true){
          break;
        }
      }
      if($is_use===false){
        //使われていない座席を返す
        return $sheat;
      }
    }
    //空き座席なし
    return null;
  }

}
