<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRemarkUserCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->string('remark', 10000)->default('')->comment('備考')->change();
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
          $table->string('body')->comment('備考')->change();
        });
    }
}
