<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //@TODO
      //複数投稿が必要になったらevaluationはtaskじゃなくてこっちを使う
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->nullable(false);
            $table->string('body')->nullable(false);
            $table->integer('evaluation')->nullable(true);
            $table->integer('create_user_id')->nullable(false);
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
        Schema::dropIfExists('reviews');
    }
}
