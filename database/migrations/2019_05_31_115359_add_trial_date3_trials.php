<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrialDate3Trials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trials', function (Blueprint $table) {
          $table->timestamp('trial_start_time3')->nullable(true)->comment('体験希望日時（第３希望）');
          $table->timestamp('trial_end_time3')->nullable(true)->comment('体験希望日時（第３希望）');
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
          $table->dropColumn('trial_start_time3');
          $table->dropColumn('trial_end_time3');
        });
    }
}
