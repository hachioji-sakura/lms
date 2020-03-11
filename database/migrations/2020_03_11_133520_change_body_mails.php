<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBodyMails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('mails', function (Blueprint $table) {
          $table->string('body', 10000)->nullable(false)->comment('内容')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('mails', function (Blueprint $table) {
          $table->string('body')->default('')->comment('内容')->change();
        });
    }
}
