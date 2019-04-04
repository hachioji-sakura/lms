<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('start_time')->index('index_start_time')->nullable(true)->comment('開始日時');
            $table->timestamp('end_time')->nullable(true)->comment('終了日時');
            $table->integer('user_id')->index('index_user_id')->comment('主催者 / 基本的に講師');
            $table->integer('lecture_id')->nullable(false)->index('index_lecture_id')->comment('レクチャーID');
            $table->string('status')->index('index_status')->default('new')->comment('新規登録:new / 確定:fix / キャンセル:cancel / 休み: rest / 出席 : presence / 欠席 : absence');
            $table->integer('exchanged_calendar_id')->default(0)->comment('振替元カレンダーID');
            $table->string('place')->comment('場所');
            $table->string('remark')->comment('備考');
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
        Schema::dropIfExists('user_calendars');
    }
}
