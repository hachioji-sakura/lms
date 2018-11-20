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
      $_user = User::create([
          'name' => 'root',
          'email' => 'root',
          'password' => 'password',
        ]);
      $_item = Manager::create([
        'user_id' => $_user->id,
        'name' => 'root',
        'kana' => 'ルート',
        ]);
    }
}
