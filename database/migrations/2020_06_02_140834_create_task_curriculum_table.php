<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskCurriculumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_curriculum', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('task_id')->index('index_task_id')->index('index_subject_id')->comment('タスクID');
          $table->integer('curriculum_id')->index('index_curriculum_id')->index('index_curriculum_id')->comment('単元ID');
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
        Schema::dropIfExists('task_curriculum');
    }
}
