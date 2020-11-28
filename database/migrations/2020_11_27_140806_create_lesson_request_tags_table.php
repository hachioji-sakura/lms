<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_request_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_request_id')->index('index_lesson_request_id')->comment('lesson_request_id');
            $table->string('tag_key')->nullable(false)->index('index_tag_key')->comment('タグキー');
            $table->string('tag_value')->comment('タグ値');
            $table->integer('create_user_id')->index('index_create_user_id')->comment('作成ユーザーID');
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
        Schema::dropIfExists('lesson_request_tags');
    }
}
