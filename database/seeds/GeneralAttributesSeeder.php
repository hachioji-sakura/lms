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
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'lesson',
          'attribute_name' => 'レッスン',
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'grade',
          'attribute_name' => '学年',
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'subject',
          'attribute_name' => '科目',
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'course',
          'attribute_name' => 'コース',
          'create_user_id' => 1,
      ]);
      $_item = GeneralAttribute::create([
          'attribute_key' => 'keys',
          'attribute_value' => 'level',
          'attribute_name' => '難易度',
          'create_user_id' => 1,
      ]);
    }
}
