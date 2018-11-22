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
      DB::beginTransaction();
      try {
        $this->create_user([
          'email' => 'root@hachioji-sakura.com',
          'password' => 'password',
          'name' => 'root',
          'kana' => 'ルート',
          'image_id' => 4
        ]);
        DB::commit();
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
      }
      catch(\Exception $e){
          DB::rollBack();
      }
    }
    private function create_user($item){
      $_user = User::create([
          'name' => $item['name'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'password' => Hash::make($item['password']),
        ]);
      $_item = Manager::create([
        'user_id' => $_user->id,
        'name' => $item['name'],
        'kana' => $item['kana'],
        'create_user_id' => 1,
        ]);

    }
}
