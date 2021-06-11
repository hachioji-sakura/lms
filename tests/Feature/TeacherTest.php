<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
use App\Models\Traits\Test;

class TeacherTest extends TestCase
{
  use Test;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStore()
    {
      echo "\n----------TeacherTest::".__FUNCTION__." Start---------------";
      $s = Student::find(1)->first();
      Auth::loginUsingId($s->user_id); //student_parents::id=1
      $response = $this->get('/teachers/create');
      $response->assertStatus(403);
      echo "\n生徒は登録権限はない=403 : OK";
      Auth::logout();

      $p = StudentParent::find(1)->first();
      Auth::loginUsingId($p->user_id); //student_parents::id=1
      $response = $this->get('/teachers/create');
      $response->assertStatus(403);
      echo "\n契約者は登録権限はない=403 : OK";
      Auth::logout();

      $t = Teacher::find(1)->first();
      Auth::loginUsingId($t->user_id); //student_parents::id=1
      $response = $this->get('/teachers/create');
      //講師は登録権限はない=403
      $response->assertStatus(403);
      echo "\n講師は登録権限はない=403 : OK";
      Auth::logout();

      $m = Manager::find(1)->first();
      Auth::loginUsingId($m->user_id); //student_parents::id=1
      $response = $this->get('/teachers/create');
      //管理者権限
      $response->assertStatus(200);
      echo "\n管理者権限=200 : OK";
      $this->register();
      echo "\n----------TeacherTest::".__FUNCTION__." End---------------";
    }
    private function register()
    {
      $c = new TeacherController;
      $request = new Request();

      $random_string = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);

      $u = Teacher::find(1)->first()->user;
      $email1 = $u->email;
      $email2 = 'test.hachiojisakura+'.$random_string.'@gmail.com';

      $data1 = [
        'name_last' => 'テスト',
        'name_first' => '先生',
        'email' => $email1,
        'locale' => 'ja',
      ];
      $data2 = [
        'name_last' => 'テスト',
        'name_first' => '先生',
        'email' => $email2,
        'locale' => 'ja',
      ];
      $this->assertDatabaseHas('common.users',  ['email'=>$data1['email']]);
      echo "\n".$email1."存在 : OK";

      $this->assertDatabaseMissing('common.users',  ['email'=>$data2['email']]);
      echo "\n".$email2."存在しない : OK";

      $res =  $c->transaction($request, function() use ($request, $data1, $data2){
        $this->post('/teachers/entry', $data1)->assertSessionHasAll(['error_message'=>'このメールアドレスはすでにユーザー登録済みです。']);
        echo "\n".$data1['email']."登録済みエラー : OK";

        $this->post('/teachers/entry', $data2)->assertSessionHasAll(['success_message'=>'仮登録メールを送信しました。']);
        $this->assertDatabaseHas('common.users',  ['email'=>$data2['email']]);

        $t = Teacher::where('name_last', $data2['name_last'])->where('name_first', $data2['name_first'])->first();
        echo "\n[register success!! for id=".$t->id."]";

        //Exceptionを発生させロールバックされる
        throw new \Exception("TeacherController::entry_store");
        return $c->api_response(200, '', '');
      }, 'test', __FILE__, __FUNCTION__, __LINE__ );
      //ロールバックしたので、email2のデータは存在しないこと
      $this->assertDatabaseHas('common.users',  ['email'=>$data1['email']]);
      $this->assertDatabaseMissing('common.users',  ['email'=>$data2['email']]);
    }
}
