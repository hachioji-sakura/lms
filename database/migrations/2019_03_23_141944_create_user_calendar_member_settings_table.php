<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarMemberSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendar_member_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_calendar_setting_id')->index('index_user_calendar_setting_id')->comment('カレンダー設定ID');
            $table->integer('user_id')->index('index_user_id')->comment('参加者設定');
            $table->string('remark')->default('')->nullable(true)->comment('備考');
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
        Schema::dropIfExists('user_calendar_member_settings');
    }
}
