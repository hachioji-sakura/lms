<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurriculumablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculumables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('curriculum_id')->nullable(false)->index('index_curriculum_id')->comment('単元ID');
            $table->string('curriculumable_type')->nullable(false)->comment('親テーブル名');
            $table->integer('curriculumable_id')->nullable(false)->index('index_curriculumable_id')->comment('親テーブルID');
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
        Schema::dropIfExists('curriculumables');
    }
}
