<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UserCalendar;

class AddCourseMinutesUserCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->integer('course_minutes')->after('lecture_id')->default(0)->comment('授業時間');
        });
        $items = UserCalendar::all();
        foreach($items as $data){
          $course_minutes = intval(strtotime($data['end_time']) - strtotime($data['start_time']))/60;
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
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->dropColumn('course_minutes');
        });

    }
}
