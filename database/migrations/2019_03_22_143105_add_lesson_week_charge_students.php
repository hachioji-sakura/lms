<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLessonWeekChargeStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charge_students', function (Blueprint $table) {
          $table->string('schedule_method')->default('week')->after('teacher_id')->comment('スケジュール登録方法：week=毎週 / month=毎月');
          $table->integer('lesson_week_count')->default(0)->after('schedule_method')->comment('schedule_method=week / 第何週を指定するときに1以上をセットする');
          $table->string('lesson_week')->default("")->after('lesson_week_count')->comment('schedule_method=week / 曜日');
          $table->string('from_time_slot')->nullable(false)->after('lesson_week')->comment('開始時分');
          $table->string('to_time_slot')->nullable(false)->after('from_time_slot')->comment('終了時分');
          $table->timestamp('enable_start_date')->nullable(true)->after('to_time_slot')->comment('設定有効開始日');
          $table->timestamp('enable_end_date')->nullable(true)->after('enable_start_date')->comment('設定有効終了日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charge_students', function (Blueprint $table) {
          $table->dropColumn('schedule_method');
          $table->dropColumn('lesson_week_count');
          $table->dropColumn('lesson_week');
          $table->dropColumn('from_time_slot');
          $table->dropColumn('to_time_slot');
          $table->dropColumn('enable_start_date');
          $table->dropColumn('enable_end_date');
        });
    }
}
