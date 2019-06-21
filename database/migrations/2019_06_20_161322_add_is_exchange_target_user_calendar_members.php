<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsExchangeTargetUserCalendarMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->integer('is_exchange_target')->default(0)->after('schedule_id')->comment('振替対象:1 / 対象外:0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->dropColumn('is_exchange_target');
        });
    }
}
