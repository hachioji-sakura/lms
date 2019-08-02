<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableStartDateUserCalendarMemberSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_member_settings', function (Blueprint $table) {
          $table->string('status')->default('new')->index('index_status')->after('user_id')->comment('ステータス/ new=新規登録 fix=有効 cancel=無効');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_member_settings', function (Blueprint $table) {
          $table->dropColumn('status');
        });
    }
}
