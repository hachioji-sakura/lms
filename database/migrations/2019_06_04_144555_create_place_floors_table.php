<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceFloorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_floors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->index('index_place_id')->comment('所在地ID');
            $table->string('name')->nullable(false)->comment('フロア名');
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
        Schema::dropIfExists('place_floors');
    }
}
