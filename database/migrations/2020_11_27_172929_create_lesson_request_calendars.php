<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_request_calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('start_time')->index('index_start_time')->nullable(true)->comment('開始日時');
            $table->timestamp('end_time')->nullable(true)->comment('終了日時');
            $table->integer('user_id')->index('index_user_id')->comment('主催者 / 基本的に講師');
            $table->integer('lesson_request_date_id')->index('index_lesson_request_date_id')->comment('lesson_request_date_id');
            $table->string('status')->index('index_status')->default('new')->comment('新規登録:new / 確定:fix / キャンセル:cancel / 休み: rest / 出席 : presence / 欠席 : absence');
            $table->string('matching_result')->default('')->comment('マッチング処理判定結果');
            $table->integer('review_value')->default(0)->comment('評価値');
            $table->integer('conflict_calendar_id')->default(0)->comment('競合カレンダーID');
            $table->integer('prev_calendar_id')->default(0)->comment('前隣接カレンダーID');
            $table->integer('next_calendar_id')->default(0)->comment('後隣接カレンダーID');
            $table->integer('place_floor_id')->default(0)->comment('場所');
            $table->string('remark')->comment('備考');
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
        Schema::dropIfExists('lesson_request_calendars');
    }
}
