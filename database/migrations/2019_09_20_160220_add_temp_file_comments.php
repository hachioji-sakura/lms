<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTempFileComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
          $table->string('s3_url')->nullable(false)->after('body')->comment('アップロードファイル　URL');
          $table->string('s3_alias')->after('body')->comment('アップロードファイル エイリアス');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
          $table->dropColumn('s3_url');
          $table->dropColumn('s3_alias');
        });
    }
}
