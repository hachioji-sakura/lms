<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbook_answers', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('question_id')->index('index_question_id')->nullable(false)->comment('問題ID');
          $table->integer('sort_no')->nullable(false)->default(0)->comment('順番');
          $table->string('answer_text')->nullable(false)->comment('回答');
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
        Schema::dropIfExists('textbook_answers');
    }
}
