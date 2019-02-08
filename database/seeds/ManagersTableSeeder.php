<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Models\Manager;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->create_user([
        'email' => 'root@hachioji-sakura.com',
        'password' => 'password',
        'name_last' => 'root',
        'name_first' => 'ユーザー',
        'kana_last' => 'ルート',
        'kana_first' => 'ユーザー',
        'image_id' => 4
      ]);
    }
    private function create_user($item){
      $_user = User::create([
          'name' => $item['name_last'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'password' => Hash::make($item['password']),
          'status' => 0,
        ]);
      $_item = Manager::create([
        'user_id' => $_user->id,
        'name_last' => $item['name_last'],
        'name_first' => $item['name_first'],
        'kana_last' => $item['kana_last'],
        'kana_first' => $item['kana_first'],
        'birth_day' => date('Y-m-d'),
        'gender' => 1,
        'create_user_id' => 1,
        ]);

    }
}
