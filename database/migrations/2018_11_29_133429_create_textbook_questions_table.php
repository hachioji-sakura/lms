<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbook_questions', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('chapter_id')->index('index_chapter_id')->nullable(false)->comment('教科書の章ID');
          $table->integer('sort_no')->nullable(false)->default(0)->comment('順番');
          $table->string('title')->nullable(false)->comment('問題文');
          $table->string('body')->default('')->comment('問題説明');
          $table->integer('score')->nullable(false)->default(0)->comment('配点');
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
        Schema::dropIfExists('textbook_questions');
    }
}
