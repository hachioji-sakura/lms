<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
class EventTest extends TestCase
{
    public $domain = 'events';
    public function _controller(){
      return new EventController;
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStore()
    {
      echo "\n----------EventTest::".__FUNCTION__." Start---------------";
      $s = Student::find(1)->first();
      Auth::loginUsingId($s->user_id); //student_parents::id=1
      $response = $this->get('/'.$this->domain.'/create');
      $response->assertStatus(403);
      echo "\n生徒は登録権限はない=403 : OK";
      Auth::logout();

      $p = StudentParent::find(1)->first();
      Auth::loginUsingId($p->user_id); //student_parents::id=1
      $response = $this->get('/'.$this->domain.'/create');
      $response->assertStatus(403);
      echo "\n契約者は登録権限はない=403 : OK";
      Auth::logout();

      $t = Teacher::find(1)->first();
      Auth::loginUsingId($t->user_id); //student_parents::id=1
      $response = $this->get('/'.$this->domain.'/create');
      //講師は登録権限はない=403
      $response->assertStatus(403);
      echo "\n講師は登録権限はない=403 : OK";
      Auth::logout();

      $m = Manager::find(1)->first();
      Auth::loginUsingId($m->user_id); //student_parents::id=1
      $response = $this->get('/'.$this->domain.'/create');
      //管理者権限
      $response->assertStatus(200);
      echo "\n管理者権限=200 : OK";
      $this->register();
      echo "\n----------EventTest::".__FUNCTION__." End---------------";
    }
    private function register()
    {
      $c = $this->_controller();
      $request = new Request();

      $data1 = [
        'event_template_id' => 1,
        'title' => '冬期講習についてのお知らせ',
        'event_from_date' => '2020-11-15',
        'event_to_date' => '2021-01-15',
        'response_from_date' => '2020-11-15',
        'response_to_date' => '2021-12-15',
        'body' => '冬期講習を実施します。\nお申込みはこちらから',
      ];
      $res =  $c->transaction($request, function() use ($request, $data1){
        $this->post('/'.$this->domain.'', $data1)->assertSessionHasAll(['success_message'=>'登録しました。']);
        echo "\n".$data1['title']."登録 : OK";
        //Exceptionを発生させロールバックされる
        throw new \Exception("EventController::store");
        return $c->api_response(200, '', '');
      }, 'test', __FILE__, __FUNCTION__, __LINE__ );
      //ロールバックしたので、データは存在しないこと
      $this->assertDatabaseMissing('lms.events',  ['title'=>$data1['title']]);
    }
}
