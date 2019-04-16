<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropScheduleIdUserCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->dropColumn('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->integer('schedule_id')->index('index_schedule_id')->default(0)->after('end_time')->comment('事務システムのカレンダーID');
        });
    }
}
