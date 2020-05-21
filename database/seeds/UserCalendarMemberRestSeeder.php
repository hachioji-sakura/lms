<?php

use Illuminate\Database\Seeder;
use App\Models\UserCalendar;

class UserCalendarMemberRestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $calendars = UserCalendar::where('status', 'rest')
                ->rangeDate('2020-04-01 00:00:00', '2020-06-01 00:00:00')
                ->get();

      foreach($calendars as $calendar){
        foreach($calendar->members as $member){
          $u = $member->user->details('students');

          if($member->status != 'rest') continue;
          if($member->rest_type == 'a1') continue;
          if($member->rest_result == '当日') continue;
          if($u['role'] != 'student') continue;
          echo "-----------------------\n";
          $member->update_rest_type('a2', '');
          echo $member->id.":".$member->rest_type.":".$member->rest_result.":".$member->exchange_limit_date."\n";
          echo "-----------------------\n";
        }
      }
    }
}
