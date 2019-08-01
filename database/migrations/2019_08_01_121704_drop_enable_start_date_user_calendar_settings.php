<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEnableStartDateUserCalendarSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_settings', function (Blueprint $table) {
          $table->date('enable_start_date')->after('status')->nullable(true)->comment('設定有効開始日')->change();
          $table->date('enable_end_date')->after('enable_start_date')->nullable(true)->comment('設定有効終了日')->change();
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
          $table->date('enable_start_date')->after('status')->nullable(true)->comment('設定有効開始日')->change();
          $table->date('enable_end_date')->after('enable_start_date')->nullable(true)->comment('設定有効終了日')->change();
        });
    }
}
