<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrialIdUserCalendarSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->integer('trial_id')->index('index_trial_id')->default(0)->after('id')->comment('設定に使った体験申し込み');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->dropColumn('trial_id');
        });
    }
}
