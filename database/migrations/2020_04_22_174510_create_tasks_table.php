<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tasks');
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('body');
            $table->integer('parent_task_id');
            $table->integer('milestone_id');
            $table->date('start_schedule');
            $table->date('start_time');
            $table->date('end_schedule');
            $table->date('end_time');
            $table->string('type');
            $table->string('status');
            $table->string('priority');
            $table->string('close_reason');
            $table->integer('target_user_id');
            $table->integer('create_user_id');
            $table->string('s3_alias');
            $table->string('s3_url');
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
        Schema::dropIfExists('tasks');
    }
}
