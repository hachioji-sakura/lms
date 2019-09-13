<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GeneralAttribute;

class GeneralAttributeUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $_add_items = [];
      foreach(config('attribute.keys') as $_config => $_config_name){
        $sort_no = 1;
        GeneralAttribute::findKey($_config)->delete();
        if(empty(config('attribute.'.$_config))) continue;
        foreach(config('attribute.'.$_config) as $index => $name){
          $_add_items[] = [
            'attribute_key' => $_config,
            'attribute_value' => $index,
            'attribute_name' => $name,
            'sort_no' => $sort_no,
            'create_user_id' => 1,
          ];
          $sort_no++;
        }
      }

      foreach($_add_items as $_add_item){
        $_item = GeneralAttribute::findKey($_add_item['attribute_key'])->findVal($_add_item['attribute_value']);
        if($_item->count()<1){
          $_item = GeneralAttribute::create($_add_item);
        }
      }
    }
}
