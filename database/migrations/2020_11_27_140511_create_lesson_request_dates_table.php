<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_request_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_request_id')->index('index_lesson_request_id')->comment('lesson_request_id');
            $table->date('day')->nullable(true)->comment('希望日');
            $table->string('from_time_slot')->nullable(false)->comment('希望時間From');
            $table->string('to_time_slot')->nullable(false)->comment('希望時間To');
            $table->integer('sort_no')->comment('希望順位');
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
        Schema::dropIfExists('lesson_request_dates');
    }
}
