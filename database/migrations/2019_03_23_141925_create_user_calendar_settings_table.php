<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendar_settings', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id')->index('index_user_id')->comment('主催者 / 基本的に講師');
          $table->string('schedule_method')->default('week')->comment('スケジュール登録方法：week=毎週 / month=毎月');
          $table->integer('lesson_week_count')->default(0)->comment('schedule_method=week / 第何週を指定するときに1以上をセットする');
          $table->string('lesson_week')->default("")->comment('schedule_method=week / 曜日');
          $table->integer('lesson_day')->default(0)->comment('schedule_method=month / 日にち指定の場合利用する');
          $table->boolean('end_of_month')->default(false)->comment('schedule_method=month / 月末の場合=true');
          $table->string('from_time_slot')->nullable(false)->comment('開始時分');
          $table->string('to_time_slot')->nullable(false)->comment('終了時分');
          $table->timestamp('enable_start_date')->nullable(true)->comment('設定有効開始日');
          $table->timestamp('enable_end_date')->nullable(true)->comment('設定有効終了日');
          $table->integer('lecture_id')->default(0)->nullable(true)->comment('レクチャーID');
          $table->string('work')->default('')->nullable(true)->comment('作業内容');
          $table->string('place')->default('')->nullable(true)->comment('場所');
          $table->string('remark')->default('')->nullable(true)->comment('備考');
          $table->integer('create_user_id')->index('index_create_user_id')->comment('作成ユーザーID');
          $table->integer('setting_id_org')->default(0)->index('index_setting_id_org')->comment('事務システム側のID');
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
        Schema::dropIfExists('user_calendar_settings');
    }
}
