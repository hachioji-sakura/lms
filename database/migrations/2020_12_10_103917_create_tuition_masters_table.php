<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('tuition_masters', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title')->nullable(true)->comment('概要');
          $table->date('start_date')->nullable(false)->comment('開始日');
          $table->date('end_date')->nullable(true)->comment('失効日');
          $table->string('grade')->comment('学年');
          $table->integer('fee')->comment('料金');
          $table->integer('lesson')->comment('部門ID');
          $table->string('course_type')->comment('コースタイプ');
          $table->integer('course_minutes')->comment('コマ時間');
          $table->integer('lesson_week_count')->comment('週当たりのコマ数');
          $table->boolean('is_exam')->comment('受験フラグ');
          $table->string('subject')->nullable(true)->comment('科目');
          $table->string('remark')->nullable(true)->comment('備考');
          $table->integer('create_user_id')->comment('起票者');
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
        Schema::dropIfExists('fee_masters');
    }
}
