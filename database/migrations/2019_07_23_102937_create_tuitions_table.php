<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('生徒ID');
            $table->integer('teacher_id')->nullable(false)->index('index_teacher_id')->comment('講師ID');
            $table->integer('tuition')->comment('受講料');
            $table->string('title')->default('')->comment('受講料名');
            $table->string('body')->default('')->comment('受講料　詳細');
            $table->string('lesson')->default('')->comment('レッスン');
            $table->string('course_type')->default('')->comment('授業形態');
            $table->integer('lesson_week_count')->default(0)->comment('通塾回数');
            $table->string('kids_lesson')->default('')->comment('習い事：そろばん、ダンスなど');
            $table->date('start_date')->nullable(true)->default(null)->comment('受講料適用開始日');
            $table->date('end_date')->nullable(true)->default(null)->comment('受講料適用終了日');
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
        Schema::dropIfExists('tuitions');
    }
}
