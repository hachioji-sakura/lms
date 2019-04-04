<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_answers', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_examination_id')->index('index_user_examination_id')->nullable(false)->comment('試験ID');
          $table->integer('question_id')->index('index_question_id')->nullable(false)->comment('問題ID');
          $table->string('answer_text')->default('')->comment('回答');
          $table->integer('judge')->nullable(false)->default(0)->comment('回答判定：1=正解、-1=不正解');
          $table->integer('is_traning')->nullable(false)->default(0)->comment('練習での回答=1 / それ以外=0');
          $table->integer('score')->nullable(false)->default(0)->comment('得点');
          $table->timestamp('start_time')->nullable(false)->comment('開始時刻');
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
        Schema::dropIfExists('user_answers');
    }
}
