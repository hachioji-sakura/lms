<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_examinations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0)->comment('リトライ元試験ID');
            $table->integer('user_id')->index('index_user_id')->nullable(false)->comment('回答者ID');
            $table->string('chapter_id')->default('')->comment('教科書の章ID');
            $table->integer('status')->nullable(false)->default(0)->comment('試験状況:実施前=0/実施中=1/練習=2/完了=10');
            $table->integer('current_question_id')->nullable(false)->default(0)->comment('直近対応問題ID');
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
        Schema::dropIfExists('user_examinations');
    }
}
