<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrialIdUserCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->string('access_key')->default('')->after('remark')->comment('アクセスキー');
          $table->integer('trial_id')->index('index_trial_id')->default(0)->after('end_time')->comment('体験授業予定id');
          $table->integer('schedule_id')->index('index_schedule_id')->default(0)->after('end_time')->comment('事務システムのカレンダーID');
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
          $table->dropColumn('access_key');
          $table->dropColumn('trial_id');
          $table->dropColumn('schedule_id');
        });
    }
}
