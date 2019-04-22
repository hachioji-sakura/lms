<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarTagSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendar_tag_settings', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_calendar_setting_id')->nullable(false)->index('index_user_calendar_setting_id')->comment('カレンダー設定ID');
          $table->string('tag_key')->nullable(false)->index('index_tag_key')->comment('タグキー');
          $table->string('tag_value')->comment('タグ値');
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
        Schema::dropIfExists('user_calendar_tag_settings');
    }
}
