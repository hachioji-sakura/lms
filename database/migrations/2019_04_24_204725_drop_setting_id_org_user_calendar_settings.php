<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSettingIdOrgUserCalendarSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->dropColumn('setting_id_org');
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
          $table->integer('setting_id_org')->default(0)->index('index_setting_id_org')->after('create_user_id')->comment('事務システム側のID');
        });
    }
}
