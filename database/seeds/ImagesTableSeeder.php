<?php

use Illuminate\Database\Seeder;
use App\Models\Image;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('images')->truncate();
      $_image = Image::create([
          'name' => 'svg_f_object_115_0bg.svg',
          'type' => 'svg',
          'size' => 8502,
          's3_url' => 'https://s3-ap-northeast-1.amazonaws.com/lms-file/user_icon/ErdHWyhklrpYNlsMvm2p1rFSGSijRop7mgqg1txL.svg',
          'alias' => '男性',
          'create_user_id' => 1,
          'publiced_at' => '2018/11/01',
      ]);
      $_image = Image::create([
          'name' => 'svg_f_object_116_0bg.svg',
          'type' => 'svg',
          'size' => 5320,
          's3_url' => 'https://s3-ap-northeast-1.amazonaws.com/lms-file/user_icon/H7H29OULySz7rpUSk7x4IuT4VkbmRwtZqgZrp3pJ.svg',
          'alias' => '女性',
          'create_user_id' => 1,
          'publiced_at' => '2018/11/01',
      ]);
      $_image = Image::create([
          'name' => 'svg_f_object_170_0bg.svg',
          'type' => 'svg',
          'size' => 8894,
          's3_url' => '	https://s3-ap-northeast-1.amazonaws.com/lms-file/user_icon/fNYxueNlpRPWmDZSibXzY7cmg2g8Cz9YftYSqLgC.svg',
          'alias' => '講師',
          'create_user_id' => 1,
          'publiced_at' => '2018/11/01',
      ]);
      $_image = Image::create([
          'name' => 'svg_f_object_86_0bg.svg',
          'type' => 'svg',
          'size' => 2796,
          's3_url' => 'https://s3-ap-northeast-1.amazonaws.com/lms-file/user_icon/fdJit7cnSve09VKk8sQRU8s0z7Bhyq1CSwnCZmDW.svg',
          'alias' => 'サクラ',
          'create_user_id' => 1,
          'publiced_at' => '2018/11/01',
      ]);

    }
}
