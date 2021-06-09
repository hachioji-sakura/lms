<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarksTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('textbooks', function (Blueprint $table) {
        $table->string('remarks')->after('url')->default('')->comment('');
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
        $table->dropColumn('remarks');
      });
    }
}
