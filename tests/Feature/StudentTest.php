<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
class StudentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test()
    {
      $this->page_request();
      $this->store();
      $this->recess();
    }
    private function page_request()
    {
      echo "\n----------StudentTest::".__FUNCTION__." Start---------------";
      $s = Student::find(1)->first();
      Auth::loginUsingId($s->user_id); //student_parents::id=1
      $response = $this->get('/students/create?student_parent_id=1');
      //生徒は登録権限はない=403
      $response->assertStatus(403);
      Auth::logout();
      $p = StudentParent::find(1)->first();
      Auth::loginUsingId($p->user_id); //student_parents::id=1
      //parent_idなし=404
      $response = $this->get('/students/create');
      $response->assertStatus(404);
      //parent_idちがいの場合=403
      $response = $this->get('/students/create?student_parent_id=264');
      $response->assertStatus(403);
      $response = $this->get('/students/create?student_parent_id=1');
      $response->assertStatus(200);
      $response->assertSee('<input type="hidden" name="student_parent_id" value="1">');
      echo "\n----------StudentTest::".__FUNCTION__." End---------------";
    }
    private function recess()
    {
      echo "\n----------StudentTest::".__FUNCTION__." Start---------------";
      $students = Student::where('recess_end_date','>', date('Y-m-d'))
                          ->where('status', 'regular')->get();
      foreach($students as $student){
        $student->recess();
      }
      echo "\n----------StudentTest::".__FUNCTION__." End---------------";
    }
    private function store()
    {
      echo "\n----------StudentTest::".__FUNCTION__." Start---------------";
      $c = new StudentController;
      $request = new Request();
      $random_string = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);
      $data1 = [
        'student_parent_id' => 1,
        'name_last' => '体験',
        'name_first' => '生徒',
        'kana_last' => 'タイケン',
        'kana_first' => 'セイト',
        'birth_day' => '2013/7/11',
        'gender' => 2,
        'grade' => 'e1',
        'school_name' => '体験学園初等部',
      ];
      $data2 = [
        'student_parent_id' => 1,
        'name_last' => '体験',
        'name_first' => '花子',
        'kana_last' => 'タイケン',
        'kana_first' => 'ハナコ',
        'birth_day' => '2013/7/11',
        'gender' => 2,
        'grade' => 'e1',
        'school_name' => '体験学園初等部',
      ];

      $this->assertDatabaseHas('common.students',  [
        'name_last' => $data1['name_last'],
        'name_first' => $data1['name_first'],
        'kana_last' => $data1['kana_last'],
        'kana_first' => $data1['kana_first'],
      ]);
      $this->assertDatabaseMissing('common.students',  [
        'name_last' => $data2['name_last'],
        'name_first' => $data2['name_first'],
        'kana_last' => $data2['kana_last'],
        'kana_first' => $data2['kana_first'],
      ]);
      $res =  $c->transaction($request, function() use ($request, $data1, $data2){
        //存在しているメールを登録しようとする場合：エラー
        $this->post('/students', $data1)->assertSessionHasAll(['error_message'=>'この生徒は登録済みです']);
        echo "\n[".$data1['name_last'].$data1['name_first']."]登録済みエラー : OK";

        $this->post('/students', $data2)->assertSessionHasAll(['success_message'=>'生徒を登録しました']);
        $this->assertDatabaseHas('common.students',  [
          'name_last' => $data1['name_last'],
          'name_first' => $data1['name_first'],
          'kana_last' => $data1['kana_last'],
          'kana_first' => $data1['kana_first'],
        ]);

        $this->assertDatabaseHas('common.students',  [
          'name_last' => $data2['name_last'],
          'name_first' => $data2['name_first'],
          'kana_last' => $data2['kana_last'],
          'kana_first' => $data2['kana_first'],
        ]);
        $s = Student::where('name_last', $data2['name_last'])->where('name_first', $data2['name_first'])->first();
        echo "\n[register success!! for id=".$s->id."]";
        throw new \Exception("StudentController::store");
        return $c->api_response(200, '', '');
      }, 'test', __FILE__, __FUNCTION__, __LINE__ );

      //ロールバックしたので、data2のデータは存在しないこと
      $this->assertDatabaseHas('common.students',  [
        'name_last' => $data1['name_last'],
        'name_first' => $data1['name_first'],
        'kana_last' => $data1['kana_last'],
        'kana_first' => $data1['kana_first'],
      ]);
      $this->assertDatabaseMissing('common.students',  [
        'name_last' => $data2['name_last'],
        'name_first' => $data2['name_first'],
        'kana_last' => $data2['kana_last'],
        'kana_first' => $data2['kana_first'],
      ]);
      echo "\n----------StudentTest::".__FUNCTION__." End---------------";
    }
}
