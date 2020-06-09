<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->nullable(false);
            $table->integer('evaluation')->nullable(false);
            //今は使わない。コメントがいるようになったら
            $table->string('comment')->nullable(true);
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
        Schema::dropIfExists('task_reviews');
    }
}
