<?php

use Illuminate\Database\Seeder;
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
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'keys',
          'attribute_name' => '定義項目',
          'sort_no' => 1,
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'lesson',
          'attribute_name' => 'レッスン',
          'sort_no' => 2,
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'subject',
          'attribute_name' => '科目',
          'sort_no' => 3,
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'course',
          'attribute_name' => 'コース',
          'sort_no' => 4,
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'grade',
          'attribute_name' => '学年',
          'sort_no' => 10,
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'level',
          'attribute_name' => '難易度',
          'sort_no' => 11,
          'create_user_id' => 1,
      ]);
    }
}
