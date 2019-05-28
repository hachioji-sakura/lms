<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusUserCalendarSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->string('status')->index('index_status')->default('new')->comment('新規登録:new / 確定:fix / 講師確認:confirm')->after('user_id');
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
          $table->dropColumn('status');
        });
    }
}
