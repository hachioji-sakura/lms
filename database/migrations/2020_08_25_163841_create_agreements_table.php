<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('agreements', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('entry_fee')->nullable(true)->comment('入会金');
          $table->integer('membership_fee')->nullable(true)->comment('会費');
          $table->datetime('entry_date')->nullable(true)->comment('入会日');
          $table->string('status')->comment('ステータス')->index('index_status');
          $table->integer('student_id')->comment('生徒ID')->index('index_student_id');
          $table->integer('lesson_week_count')->comment('週コマ数');
          $table->integer('lesson_time')->comment('受講時間(分)');
          $table->string('course_type')->comment('授業形態');
          $table->string('grade')->comment('学年');
          $table->string('lesson_type')->comment('部門');
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
        Schema::connection('mysql_common')->dropIfExists('agreements');
    }
}
