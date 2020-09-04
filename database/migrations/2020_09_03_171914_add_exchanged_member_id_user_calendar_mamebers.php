<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExchangedMemberIdUserCalendarMamebers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_calendar_members', function (Blueprint $table) {
            //
            $table->integer('exchanged_member_id')->nullable(true)->after('remark')->comment('代講元ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->dropColumn('exchanged_member_id');
        });
    }
}
