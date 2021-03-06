<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDifficultyTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('textbooks', function (Blueprint $table) {
        $table->string('difficulty')->nullable(false)->default('')->comment('難易度')->after('explain');
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
          $table->dropColumn('difficulty');
        });
    }
}
