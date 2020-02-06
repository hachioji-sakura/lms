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
        if(empty(config('attribute.'.$_config))) continue;
        GeneralAttribute::findKey($_config)->delete();
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
      $this->create_temporary_attribute();
    }
    public function create_temporary_attribute(){
      $url = './config/attributes.php';
      $fields = ['attribute_name', 'parent_attribute_key', 'parent_attribute_value', 'sort_no'];
      $attributes = GeneralAttribute::orderBy('attribute_key')->orderBy('sort_no')->get();
      $contents = "<?php  return array(";
      $key = "";
      foreach($attributes as $attribute){
        if(empty($key) || $key!=$attribute->attribute_key){
          if(!empty($key)){
            $contents .= "\t],\n";
          }
          $key = $attribute->attribute_key;
          $contents .= "\t\"".$attribute->attribute_key."\" => [\n";
        }
        $contents .= "\t\t\"".$attribute->attribute_value."\" => [\n";
        foreach($fields as $field){
          $contents .= "\t\t\t\"".$field.'" => "'.$attribute[$field]."\",\n";
        }
        $contents .= "\t\t],\n";
      }
      $contents .= "],); ?>";
      $bytes_written = \File::put($url, $contents);
      if ($bytes_written === false) {
        \Log::error("create_temporary_attribute: file_put error!");
        return false;
      }
      return true;
    }
}
