<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserCalendar;

class PlaceFloor extends Model
{
  protected $connection = 'mysql_common';
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
    public function name(){
      if(app()->getLocale()=='en') return $this->name_en;
      return $this->name;
    }
    public function get_free_seat($start_time, $end_time){
      //指定した時間帯・フロアのカレンダーを取得(新規、キャンセル、休み、休講は利用扱いではない）
      $calendars = UserCalendar::searchDate($start_time, $end_time)
                ->findStatuses(['cancel', 'rest', 'lecture_cancel', 'new'], true)
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
    //TODO 事務システムがcommon.place_floorsを参照するようになれば、この変換は不要
    //事務システム定義　→　本システム定義
    static protected function _offie_system_place_convert($place){
      $mapping_office_system_place = [
        1=> '本校',
        2=> '南口校',
        3=> '北口校3F',
        4=> '北口校4F',
        5=> '北口校4F', //北口校
        6=> 'アローレ校',//アローレ
        7=> 'ダットッチ校',
        8=> '日野市豊田校',
        9=> '国立校',
      ];

      if(isset($mapping_office_system_place[$place])){
        $floor = PlaceFloor::where('name', $mapping_office_system_place[$place])->first();
        return $floor;
      }
      return null;
    }
    //TODO 事務システムがcommon.place_floorsを参照するようになれば、この変換は不要
    //本システム定義　→　事務システム定義
    public function _convert_offie_system_place(){
      $mapping_office_system_place = [
        1=> '本校',
        2=> '南口校',
        3=> '北口校3F',
        4=> '北口校4F',
        5=> '北口校4F', //北口校
        6=> 'アローレ校',//アローレ
        7=> 'ダットッチ校',
        8=> '日野市豊田校',
        9=> '国立校',
      ];

      foreach($mapping_office_system_place as $i => $v){
        if($v==$this->name) return $v;
      }
      return 0;
    }
}
