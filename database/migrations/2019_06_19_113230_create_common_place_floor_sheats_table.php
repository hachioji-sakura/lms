<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonPlaceFloorSheatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('place_floor_sheats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_floor_id')->nullable(false)->index('index_place_floor_id')->comment('フロアID');
            $table->string('name')->nullable(false)->comment('席名');
            $table->integer('sort_no')->default(0)->comment('表示順');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->dropIfExists('place_floor_sheats');
    }
}
