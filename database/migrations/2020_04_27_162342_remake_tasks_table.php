<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemakeTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('tasks');
        Schema::create('tasks', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title')->nullable(false);
          $table->string('remarks')->nullable(true);
          $table->integer('milestone_id')->nullable(true);
          $table->string('type')->nullable(true);
          $table->string('status')->nullable(false);
          $table->integer('target_user_id')->nullable(false)->index('index_target_user_id');
          $table->integer('create_user_id')->nullable(false)->index('index_create_user_id');
          $table->integer('evaluation')->nullable(true);
          $table->string('priority')->nullable(true);
          $table->string ('s3_url')->nullable(true);
          $table->string ('s3_alias')->nullable(true);
          $table->date('start_schedule')->nullable(false);
          $table->date('start_date')->nullable(true);
          $table->date('end_schedule')->nullable(false);
          $table->date('end_date')->nullable(true);
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
        //
        Schema::dropIfExists('tasks');
    }
}
