<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('agreement_statements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->comment('生徒ID');
            $table->integer('teacher_id')->comment('先生ID');
            $table->integer('agreement_id')->comment('契約ID');
            $table->integer('tuition')->comment('料金');
            $table->string('title')->nullable(true)->comment('概要');
            $table->integer('lesson_id')->comment('レッスンID');
            $table->string('course_type')->comment('コースタイプ');
            $table->integer('course_minutes')->comment('コマ時間');
            $table->string('subject')->nullable(true)->comment('科目');
            $table->string('grade')->comment('学年');
            $table->integer('lesson_week_count')->comment('週当たりのコマ数');
            $table->boolean('is_exam')->comment('受験フラグ');
            $table->string('remark')->nullable(true)->comment('備考');
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
        Schema::connection('mysql_common')->dropIfExists('agreement_statements');
    }
}
