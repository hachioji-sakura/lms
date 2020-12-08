<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccessKeyEventUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_users', function (Blueprint $table) {
          $table->string('access_key')->default('')->after('status')->comment('アクセスキー');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_users', function (Blueprint $table) {
          $table->dropColumn('access_key');
        });
    }
}
