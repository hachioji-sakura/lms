<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GeneralAttribute;
class GeneralAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      GeneralAttribute::truncate();

      $_add_items = [];
      foreach(config('charge_subjects') as $grade => $subject_group){
        $sort_no = 1;
        $grade_val = 'o';
        if($grade==='小学') $grade_val = 'e';
        else if($grade==='中学') $grade_val = 'j';
        else if($grade==='高校') $grade_val = 'h';
        foreach($subject_group as $subject => $subject_data){
          if(isset($subject_data['items'])){
            foreach($subject_data['items'] as $index => $name){
              $_add_items[] = [
                'attribute_key' => 'charge_subject',
                'attribute_value' => $index,
                'attribute_name' => $name,
                'parent_attribute_key' => 'charge_grade',
                'parent_attribute_value' => $grade_val,
                'sort_no' => $sort_no,
                'create_user_id' => 1,
              ];
              $_add_items[] = [
                'attribute_key' => 'charge_subject_level_item',
                'attribute_value' => $index.'_level',
                'parent_attribute_key' => 'charge_subject',
                'parent_attribute_value' => $index,
                'attribute_name' => $name.'('.$grade.')',
                'sort_no' => $sort_no,
                'create_user_id' => 1,
              ];
              $sort_no++;
            }
          }
          else{
            $_add_items[] = [
              'attribute_key' => 'charge_subject',
              'attribute_value' => $subject,
              'attribute_name' => $subject_data['name'].'('.$grade.')',
              'parent_attribute_key' => 'charge_grade',
              'parent_attribute_value' => $grade_val,
              'sort_no' => $sort_no,
              'create_user_id' => 1,
            ];
            $_add_items[] = [
              'attribute_key' => 'charge_subject_level_item',
              'attribute_value' => $subject.'_level',
              'attribute_name' => $subject_data['name'].'('.$grade.')',
              'parent_attribute_key' => 'charge_subject',
              'parent_attribute_value' => $subject,
              'sort_no' => $sort_no,
              'create_user_id' => 1,
            ];
            $sort_no++;
          }
        }
      }
      GeneralAttribute::findKey('charge_subject_level_item')->delete();

      foreach(config('attribute.keys') as $_config => $_config_name){
        $sort_no = 1;
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
      $sort_no = 1;
      foreach(config('attribute.lesson_week') as $week_code => $week_name){
          foreach(config('attribute.lesson_time') as $index => $name){
          $_add_items[] = [
            'attribute_key' => 'lesson_'.$week_code.'_time',
            'attribute_value' => $index,
            'attribute_name' => $name,
            'parent_attribute_key' => 'lesson_week',
            'parent_attribute_value' => $week_code,
            'sort_no' => $sort_no,
            'create_user_id' => 1,
          ];
          $sort_no++;
        }
      }
      $sort_no = 1;
      foreach(config('grade') as $index => $name){
        $charge_grade = substr($index,0,1);
        if($charge_grade!=='e' && $charge_grade!=='j' && $charge_grade!=='h'){
          $charge_grade = 'o';
        }
        $_add_items[] = [
          'attribute_key' => 'grade',
          'attribute_value' => $index,
          'attribute_name' => $name,
          'parent_attribute_key' => 'charge_grade',
          'parent_attribute_value' => $charge_grade,
          'sort_no' => $sort_no,
          'create_user_id' => 1,
        ];
        $sort_no++;
      }

      foreach($_add_items as $_add_item){
        $_item = GeneralAttribute::findKey($_add_item['attribute_key'])->findVal($_add_item['attribute_value']);
        if($_item->count()<1){
          $_item = GeneralAttribute::create($_add_item);
        }
      }
      $controller = new Controller;
      $req = new Request;
      $url = config('app.url').'/import/attributes';
      $res = $controller->call_api($req, $url, 'POST');
    }
}
