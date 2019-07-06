<?php

use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\PlaceFloor;
use App\Models\PlaceFloorSheat;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Place::truncate();
      PlaceFloor::truncate();
      PlaceFloorSheat::truncate();

      $i=0;
      foreach(config('lesson_place') as $key => $p){
        $i++;
        $place = Place::create([
          'name' => $p['name'],
          'name_en' => $p['name_en'],
          'post_no' => $p['postno'],
          'address' => $p['address'],
          'phone_no' => '',
          'sort_no' => $i,
        ]);
        $place_id = $place->id;
        $j=0;
        foreach($p['floors'] as $f){
          $j++;
          $count = $f['sheat_count'];
          $floor = PlaceFloor::create([
            'place_id' => $place_id,
            'name' => $f['name'],
            'name_en' => $f['name_en'],
            'sort_no' => $j,
          ]);
          $floor_id = $floor->id;
          for($d=0;$d<$count;$d++){
            PlaceFloorSheat::create([
              'name' => $d+1,
              'sort_no' =>$d+1,
              'place_floor_id' => $floor_id,
            ]);
          }
        }
      }
    }
}
