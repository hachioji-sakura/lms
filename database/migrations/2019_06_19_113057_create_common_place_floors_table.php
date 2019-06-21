<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonPlaceFloorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('place_floors', function (Blueprint $table) {
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
        Schema::connection('mysql_common')->dropIfExists('place_floors');
    }
}
