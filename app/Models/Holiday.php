<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
  //Googleカレンダーから祝日を取得
  static protected function holiday_update() {
    $url = "https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv";

    //JSON形式で取得した情報を配列に変換
    $results = file_get_contents($url);
    $results = mb_convert_encoding($results, 'UTF-8', 'sjis-win');
    $temp = tmpfile();
    $csv  = array();

    fwrite($temp, $results);
    rewind($temp);

    while (($results = fgetcsv($temp, 0, ",")) !== FALSE) {
        $csv[] = $results;
    }
    fclose($temp);
    $i=0;
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
