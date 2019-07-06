<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameEnPlaceFloors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('place_floors', function (Blueprint $table) {
          $table->string('name_en')->nullable(true)->default('')->after('name')->comment('名称（英語）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('place_floors', function (Blueprint $table) {
          $table->dropColumn('name_en');
        });
    }
}
