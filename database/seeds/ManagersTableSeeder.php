<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\Manager;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentRelation;
use App\Models\UserTag;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->create_manager([
        'email' => 'root@hachioji-sakura.com',
        'password' => 'password',
        'name_last' => 'root',
        'name_first' => 'ユーザー',
        'kana_last' => 'ルート',
        'kana_first' => 'ユーザー',
        'image_id' => 4,
      ]);

      $this->create_student([
        'email' => 'trial@hachioji-sakura.com',
        'password' => 'password',
        'name_last' => '体験',
        'name_first' => 'ユーザー',
        'kana_last' => 'タイケン',
        'kana_first' => 'ユーザー',
        'image_id' => 1,
        'student_no' => -1,
      ]);
      $controller = new Controller;
      $req = new Request;
      $url = config('app.url').'/import/users';
      $res = $controller->call_api($req, $url, 'POST');
      $url = config('app.url').'/import/concealment';
      $res = $controller->call_api($req, $url, 'POST');
    }
    private function create_manager($item){
      $_user = User::where('email', $item['email'])->first();
      if(isset($_user)){
        return;
      }
      $_user = User::create([
          'name' => $item['name_last'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'password' => Hash::make($item['password']),
          'status' => 0,
        ]);
      $_item = Manager::create([
        'user_id' => $_user->id,
        'status' => 'regular',
        'name_last' => $item['name_last'],
        'name_first' => $item['name_first'],
        'kana_last' => $item['kana_last'],
        'kana_first' => $item['kana_first'],
        'birth_day' => date('Y-m-d'),
        'gender' => 1,
        'create_user_id' => 1,
        ]);
    }
    private function create_student($item){
      $_user = User::where('email', $item['email'])->first();
      if(isset($_user)){
        return;
      }

      $user1 = User::create([
          'name' => $item['name_last'],
          'email' => $item['email'],
          'image_id' => $item['image_id'],
          'password' => Hash::make($item['password']),
          'status' => 0,
        ]);
      $parent = StudentParent::create([
        'user_id' => $user1->id,
        'status' => 'regular',
        'name_last' => $item['name_last'],
        'name_first' => $item['name_first'],
        'kana_last' => $item['kana_last'],
        'kana_first' => $item['kana_first'],
        'birth_day' => date('Y-m-d'),
        'phone_no' => '',
        'address' => '',
        'create_user_id' => 1,
        ]);

      $user2 = User::create([
          'name' => $item['name_last'],
          'email' => $item['student_no'],
          'image_id' => $item['image_id'],
          'password' => Hash::make($item['password']),
          'status' => 0,
        ]);
      $student = Student::create([
        'user_id' => $user2->id,
        'status' => 'regular',
        'name_last' => $item['name_last'],
        'name_first' => $item['name_first'],
        'kana_last' => $item['kana_last'],
        'kana_first' => $item['kana_first'],
        'birth_day' => date('Y-m-d'),
        'create_user_id' => 1,
        ]);

      StudentRelation::create([
        'student_id' => $student->id,
        'student_parent_id' => $parent->id,
        'create_user_id' => 1,
      ]);
      $this->store_user_tag($student->user_id, 'student_no', $item['student_no'], false);
      $this->store_user_tag($student->user_id, 'grade', 'toddler');

    }
    private function store_user_tag($user_id, $key, $val){
      if(empty($user_id)) return false;
      if(empty($key)) return false;
      if(empty($val)) return false;

      UserTag::create([
        'user_id' => $user_id,
        'tag_key' => $key,
        'tag_value' => $val,
        'create_user_id' => 1
      ]);
      return true;
    }

}
