<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UserCalendarSetting;

class AddCourseMinutesUserCalendarSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->integer('course_minutes')->after('lecture_id')->default(0)->comment('授業時間');
        });
        $items = UserCalendarSetting::all();
        foreach($items as $data){
          $course_minutes = intval(strtotime('2000-01-01 '.$data['to_time_slot']) - strtotime('2000-01-01 '.$data['from_time_slot']))/60;
          $data->update(['course_minutes' => $course_minutes]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->dropColumn('course_minutes');
        });
    }
}
