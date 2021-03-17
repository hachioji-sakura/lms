<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImageIdTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `textbooks` CHANGE COLUMN `image_id` `image_id` INT NOT NULL DEFAULT 0 COMMENT '本の写真など' AFTER `url`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('textbooks', function (Blueprint $table) {
          $table->string('image_id')->default(0)->change();
        });
    }
}
