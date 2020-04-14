<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentAskIdAsks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asks', function (Blueprint $table) {
          $table->integer('parent_ask_id')->default(0)->index('index_parent_ask_id')->comment('親依頼ID')->after("type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return
     */
    public function down()
    {
        Schema::table('asks', function (Blueprint $table) {
          $table->dropColumn('parent_ask_id');
        });
    }
}
