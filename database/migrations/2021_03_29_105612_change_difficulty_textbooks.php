<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDifficultyTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('textbooks', function (Blueprint $table) {
      $table->string('difficulty')->nullable(false)->default('')->comment('難易度')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('textbooks', function (Blueprint $table) {
      $table->integer('difficulty')->after('explain')->default(0)->comment('10 => 簡単, 20 => 普通, 30 => 難しい');
    });
  }
}
