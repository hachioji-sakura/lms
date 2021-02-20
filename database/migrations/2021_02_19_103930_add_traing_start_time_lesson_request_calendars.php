<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkLessonRequestCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_request_calendars', function (Blueprint $table) {
          $table->string('traing_start_time')->comment('演習開始時刻')->after('end_time');
          $table->string('traing_end_time')->comment('演習終了時刻')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_request_calendars', function (Blueprint $table) {
          $table->dropColumn('traing_start_time');
          $table->dropColumn('traing_end_time');
        });
    }
}
