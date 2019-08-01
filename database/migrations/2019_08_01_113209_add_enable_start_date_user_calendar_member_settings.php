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
          $table->date('enable_start_date')->after('status')->nullable(true)->comment('設定有効開始日');
          $table->date('enable_end_date')->after('enable_start_date')->nullable(true)->comment('設定有効終了日');
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
          $table->dropColumn('enable_start_date');
          $table->dropColumn('enable_end_date');
        });
    }
}
