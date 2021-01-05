<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMatchingResultLessonRequestCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_request_calendars', function (Blueprint $table) {
          $table->string('matching_result')->after('id')->default('')->comment('マッチング処理判定結果');
          $table->integer('lesson_request_date_id')->index('index_lesson_request_date_id')->after('id')->default('')->comment('lesson_requests_dates_id');
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
          $table->dropColumn('matching_result');
          $table->dropColumn('lesson_request_date_id');
        });
    }
}
