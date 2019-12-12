<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $controller = new Controller;
      $res = $controller->getHolidays();
      $controller->send_slack('国民の休日CSV取り込み', 'info');
      foreach($res as $i => $d){
        if($i==0) continue;
        $this->add($d[0], $d[1], true, false);
      }
      $private_holidays = ['12-30', '12-31', '1-1', '1-2', '1-3'];
      $y = date('Y');
      foreach($private_holidays as $day){
        $holiday = $this->add($y.'-'.$day, '休校日', false, true);
      }
      $y = $y+1;
      foreach($private_holidays as $day){
        $holiday = $this->add($y.'-'.$day, '休校日', false, true);
        $holiday->update(['is_private_holiday' => 1]);
      }
    }
    private function add($day, $remark, $is_public=false, $is_private=false){
      $day = date('Y-m-d', strtotime($day));
      if(strtotime($day) < strtotime('1999-01-01')) return null;
      $holiday = Holiday::where('date', $day)->first();
      if(!isset($holiday)){
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
