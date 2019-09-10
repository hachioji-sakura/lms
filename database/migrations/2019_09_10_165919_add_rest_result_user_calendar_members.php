<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestResultUserCalendarMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->string('rest_result')->nullable(true)->default('')->comment('休み判定理由')->after('rest_type');
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
          $table->dropColumn('rest_result');
        });
    }
}
