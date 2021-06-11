<?php

namespace App\Models;

use CSVReader;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Holiday
 *
 * @property int $id
 * @property string $date 日付
 * @property string $remark 説明
 * @property int $is_public_holiday 国民の休日の場合=1
 * @property int $is_private_holiday 塾の休日の場合=1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday isPublicPrivate($is_public = true, $is_private = true)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday query()
 * @mixin \Eloquent
 */
class Holiday extends Model
{
  protected $table = 'common.holidays';
  protected $guarded = array('id');

  public static $rules = array(
      'date' => 'required',
  );
  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeIsPublicPrivate($query, $is_public=true, $is_private=true)
  {
    $query = $query->where(function($query)use($is_public, $is_private){
        if($is_public==true){
          $query = $query->orWhere('is_public_holiday','1');
        }
        if($is_private==true){
          $query = $query->orWhere('is_private_holiday','1');
        }
    });
    return $query;
  }

  public function is_public_holiday(){
    if($this->is_public_holiday==1){
      return true;
    }
    return false;
  }
  public function is_private_holiday(){
    if($this->is_private_holiday==1){
      return true;
    }
    return false;
  }
  //内閣府HPから祝日を取得
  static protected function holiday_update() {
    $csv =  CSVReader::readByUrl('https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv');
    
    foreach($csv as $i => $d){
      if($i==0) continue;
      Holiday::add($d[0], $d[1], true, false);
    }

    //12-30～1-3までは休校日扱い
    $private_holidays = ['12-30', '12-31', '1-1', '1-2', '1-3'];
    $y = date('Y'); //今年,来年、再来年分ぐらいを登録しておく

    for($i=$y;$i<$y+3;$i++){
      foreach($private_holidays as $day){
        $holiday = Holiday::add($i.'-'.$day, '休校日', false, true);
        $holiday->update(['is_private_holiday' => 1]);
      }
    }
  }

  static protected function add($day, $remark, $is_public=false, $is_private=false){
    $day = date('Y-m-d', strtotime($day));
    if(strtotime($day) < strtotime('1999-01-01')) return null;
    $holiday = Holiday::where('date', $day)->first();
    if(!isset($holiday)){
      //存在しない場合登録
      $item = ['date' => $day, 'remark' => $remark];
      if($is_public==true){
        $item['is_public_holiday'] = 1;
      }
      if($is_private==true){
        $item['is_private_holiday'] = 1;
      }
      $holiday = Holiday::create($item);
    }
    return $holiday;
  }
}
