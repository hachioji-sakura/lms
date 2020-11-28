<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->index('index_type')->comment('trial:体験、season_lesson:季節講習');
            $table->integer('student_parent_id')->index('index_parent_id')->comment('保護者ID');
            $table->integer('student_id')->index('index_student_id')->comment('生徒ID');
            $table->string('status')->index('index_status')->default('new')->comment('新規登録:new / 確定:fix / キャンセル:cancel');
            $table->string('remark')->default('')->comment('備考');
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
        Schema::dropIfExists('lesson_requests');
    }
}
