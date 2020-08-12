<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('mysql_common')->create('action_logs', function (Blueprint $table) {
          $table->increments('id');
          $table->string('server_name')->comment('SERVER_NAME');
          $table->string('server_ip')->comment('SERVER_ADDR');
          $table->string('method')->nullable(false)->comment('REQUEST_METHOD');
          $table->string('client_ip')->comment('REMOTE ADDRESS');
          $table->string('session_id')->comment('SESSION_ID');
          $table->integer('login_user_id')->comment('login_user_id');
          $table->string('user_agent')->comment('HTTP USER AGENT');
          $table->string('language')->comment('HTTP ACCEPT LANGUAGE');
          $table->string('url')->comment('REQUEST_URI');
          $table->string('referer')->comment('HTTP REFERER');
          $table->string('post_param', 10000)->default('')->comment('post変数');
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->dropIfExists('action_logs');
    }
}
