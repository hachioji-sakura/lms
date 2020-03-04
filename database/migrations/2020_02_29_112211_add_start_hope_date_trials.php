<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartHopeDateTrials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trials', function (Blueprint $table) {
          $table->date('schedule_start_hope_date')->nullable(true)->after('status')->comment('授業開始希望日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trials', function (Blueprint $table) {
          $table->dropColumn('schedule_start_hope_date');
        });
    }
}
