<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedUserablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_userables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->index('index_user_id')->comment('ユーザーID');
            $table->string('shared_userable_type')->nullable(false)->comment('親テーブル名');
            $table->integer('shared_userable_id')->nullable(false)->index('index_userable_id')->comment('親テーブルID');
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
        Schema::dropIfExists('shared_userables');
    }
}
