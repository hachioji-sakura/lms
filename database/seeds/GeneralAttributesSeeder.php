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
      $_add_items = [
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'keys',
            'attribute_name' => '定義項目',
            'sort_no' => 1,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'lesson',
            'attribute_name' => 'レッスン',
            'sort_no' => 2,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'subject',
            'attribute_name' => '科目',
            'sort_no' => 3,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'course',
            'attribute_name' => 'コース',
            'sort_no' => 4,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'grade',
            'attribute_name' => '学年',
            'sort_no' => 10,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'level',
            'attribute_name' => '難易度',
            'sort_no' => 11,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'place',
            'attribute_name' => '場所',
            'sort_no' => 21,
            'create_user_id' => 1,
        ]
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'absence_type',
            'attribute_name' => '休み種別',
            'sort_no' => 22,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'jyukensei',
            'attribute_name' => '受験生フラグ',
            'sort_no' => 30,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'fee_free',
            'attribute_name' => '受講料無料フラグ',
            'sort_no' => 90,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'keys',
            'attribute_value' => 'grade_adj',
            'attribute_name' => '学年調整値(留年など)',
            'sort_no' => 91,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'jyukensei',
            'attribute_value' => '1',
            'attribute_name' => '受験生',
            'sort_no' => 90,
            'create_user_id' => 1,
        ],
        [
            'attribute_key' => 'fee_free',
            'attribute_value' => '1',
            'attribute_name' => '受講料無料',
            'sort_no' => 90,
            'create_user_id' => 1,
        ],

      ];
      foreach($_add_items as $_add_item){
        $_item = GeneralAttribute::findKey($_add_item['attribute_key'])->findVal($_add_item['attribute_value']);
        if($_item->count()<1){
          $_item = GeneralAttribute::create($_add_item);
        }
      }
    }
}
