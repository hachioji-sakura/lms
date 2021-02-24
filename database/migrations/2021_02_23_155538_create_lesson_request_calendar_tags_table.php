<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestCalendarTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_request_calendar_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_request_calendar_id')->nullable(false)->index('index_lesson_request_calendar_id')->comment('カレンダーID');
            $table->string('tag_key')->nullable(false)->index('index_tag_key')->comment('タグキー');
            $table->string('tag_value')->comment('タグ値');
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
        Schema::dropIfExists('lesson_request_calendar_tags');
    }
}
