<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\StudentParentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;

class StudentParentTest extends TestCase
{
 function testSignup()
    {
      echo "\n----------StudentParentTest::".__FUNCTION__." Start---------------";
      $response = $this->get('/signup');
      $response->assertStatus(200);
      $this->signup();
      echo "\n----------StudentParentTest::".__FUNCTION__." End---------------";
    }

    private function signup()
    {
      $c = new StudentParentController;
      $request = new Request();
      $random_string = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);
      $u = Teacher::find(1)->first()->user;
      $email1 = $u->email;
      $email2 = 'test.hachiojisakura+'.$random_string.'@gmail.com';
      $this->assertDatabaseHas('common.users',  ['email'=>$email1]);
      $this->assertDatabaseMissing('common.users',  ['email'=>$email2]);

      $res =  $c->transaction($request, function() use ($request, $email1, $email2){
        //存在しているメールを登録しようとする場合：エラー
        $this->post('/signup', ['email'=>$email1])
             ->assertSee('このメールアドレスは、すでに登録済みです');
         echo "\n".$email1."登録済みエラー : OK";

         //存在していないメールを登録しようとする場合：正常動作
        $this->post('/signup', ['email'=>$email2])
             ->assertSee($email2.'に本登録用のURLを送信しました');

        //email2のデータが存在すること
        $this->assertDatabaseHas('common.users',  ['email'=>$email2]);
        echo "\n[register success!! for email=".$email2."]";

        //Exceptionを発生させロールバックされる
        throw new \Exception("StudentParentController::entry_store");
        return $c->api_response(200, '', '');
      }, 'test', __FILE__, __FUNCTION__, __LINE__ );

      //ロールバックしたので、email2のデータは存在しないこと
      $this->assertDatabaseHas('common.users',  ['email'=>$email1]);
      $this->assertDatabaseMissing('common.users',  ['email'=>$email2]);
    }
}
