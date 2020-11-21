<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUrlActionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('action_logs', function (Blueprint $table) {
          $table->longText('url')->comment('REQUEST_URI')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('action_logs', function (Blueprint $table) {
          $table->string('url')->comment('REQUEST_URI')->change();
        });
    }
}
