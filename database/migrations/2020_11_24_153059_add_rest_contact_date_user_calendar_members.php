<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UserCalendarMember;
use App\Models\ActionLog;

class AddRestContactDateUserCalendarMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->timestamp('rest_contact_date')->nullable(true)->after('schedule_id')->comment('休み連絡日');
        });

        $members = UserCalendarMember::where('status', 'rest')->get();
        foreach($members as $member){
          $role = $member->user->get_role();
          if($role!='student') continue;
          $log = ActionLog::where('method', 'POST')
                    ->where('url', 'like', '%/calendars/'.$member->calendar_id.'/status_update/rest')
                    ->whereRaw('login_user_id in (select user_id from common.student_parents)',[])
                    ->first();
          $u = null;
          if(isset($log)){
            \Log::warning("is_single?:".$member->calendar->is_group());
            $u = $log->created_at;
          }
          else {
            $log = ActionLog::where('method', 'POST')
                      ->where('url', 'like', '%/calendars/'.$member->calendar_id.'')
                      ->where('url', 'not like', '%/status_update/%')
                      ->whereRaw('login_user_id in (select user_id from common.student_parents)',[])
                      ->where('post_param', 'not like', '%_status":"rest"%')
                      ->first();
            if(isset($log)){
              \Log::warning("is_group?:".$member->calendar->is_group());
              $u = $log->created_at;
            }
          }
          if($u==null) continue;
          $member->update(['rest_contact_date' => $u]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->dropColumn('rest_contact_date');
        });
    }
}
