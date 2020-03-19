<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostNoManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
        $table->string('post_no')->nullable(true)->after('phone_no');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
        $table->dropColumn('post_no');
      });
    }
}
