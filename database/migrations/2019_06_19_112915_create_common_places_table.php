<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('places', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name')->nullable(false)->comment('');
          $table->integer('sort_no')->default(0)->comment('表示順');
          $table->string('post_no')->nullable(true)->comment('郵便番号');
          $table->string('addreess')->nullable(true)->comment('住所');
          $table->string('phone_no')->nullable(true)->comment('連絡先');
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
        Schema::connection('mysql_common')->dropIfExists('places');
    }
}
