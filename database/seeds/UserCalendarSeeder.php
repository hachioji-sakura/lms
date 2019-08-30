<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarTag;
use App\Models\UserCalendarSetting;
use App\Models\UserCalendarMemberSetting;
use App\Models\UserCalendarTagSetting;
use App\Models\ChargeStudent;
use App\Models\ChargeStudentTag;

class UserCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      UserCalendar::truncate();
      UserCalendarMember::truncate();
      UserCalendarTag::truncate();
      UserCalendarSetting::truncate();
      UserCalendarMemberSetting::truncate();
      UserCalendarTagSetting::truncate();
      ChargeStudent::truncate();
      ChargeStudentTag::truncate();
      $controller = new Controller;
      $req = new Request;
      $url = config('app.url').'/import/repeat_schedules';
      $res = $controller->call_api($req, $url, 'POST');
      $url = config('app.url').'/import/schedules';
      $res = $controller->call_api($req, $url, 'POST');
    }
}
