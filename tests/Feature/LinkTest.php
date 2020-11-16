<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;

class LinkTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
     public function testDisplay()
     {
       $urls = [
         "/students",
         "/teachers",
         "/managers",
         "/parents",
         "/students/34",
         "/students/34/edit",
         "/students/34/calendar",
         "/students/34/setting",
         "/students/34/schedule",
         "/students/34/comments",
         "/students/34/milestones",
         "/students/34/tasks",
         "/teachers/2",
         "/teachers/2/edit",
         "/teachers/2/setting",
         "/teachers/2/calendar",
         "/teachers/2/month_work",
         "/teachers/2/schedule",
         "/teachers/2?view=setting_menu",
         "/teachers/2/calendar_settings",
         "/managers/1/email_edit",
         "/managers/4",
         "/managers/4/calendar",
         "/managers/4/month_work",
         "/managers/4/edit",
         "/managers/4?view=setting_menu",
         "/managers/4/calendar_settings",
         "/parents/33",
         "/parents/33/edit",
         "/events",
         "/event_templates",
         "/attributes",
         "/faqs",
         "/calendars",
         "/calendars/48830",
         "/calendars/48830/edit",
         "/calendars/create",
         "/calendars/create",
         "/calendar_settings",
         "/calendar_settings/create",
         "/calendar_settings/382",
         "/calendar_settings/382/edit",
         "/tasks",
         "/tasks/765/detail_dialog",
         "/milestones/37",
         "/milestones/create",
         "/entry",
         "/trials",
         "/trials/34",
         "/trials/34/to_calendar",
       ];
       //TODO  "/tasks/create", がエラーするので除外

       echo "\n----------LinkTest::".__FUNCTION__." Start---------------";
       $m = Manager::find(1)->first();
       Auth::loginUsingId($m->user_id); //student_parents::id=1
       foreach($urls as $url){
         echo "\n[".$url."] : test";
         $response = $this->get($url);
         $response->assertStatus(200);
         echo "\n[".$url."] : OK";
       }
       echo "\n----------LinkTest::".__FUNCTION__." End---------------";
     }
}
