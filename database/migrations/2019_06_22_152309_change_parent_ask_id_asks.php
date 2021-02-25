<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeParentAskIdAsks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `asks` CHANGE COLUMN `parent_ask_id` `parent_ask_id` INT NOT NULL DEFAULT 0 COMMENT '親依頼ID' AFTER `type`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asks', function (Blueprint $table) {
          $table->string('parent_ask_id')->default(0)->change();
        });
    }
}
