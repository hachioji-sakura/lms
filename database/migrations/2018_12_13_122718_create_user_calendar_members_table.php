<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendar_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendar_id')->nullable(false)->index('index_calendar_id')->comment('カレンダーID');
            $table->integer('user_id')->nullable(false)->index('index_user_id')->comment('対象ユーザーID');
            $table->string('status')->default('new')->comment('新規登録:new / 確定:fix / キャンセル:cancel / 休み: rest / 出席 : presence / 欠席 : absence');
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
        Schema::dropIfExists('user_calendar_members');
    }
}
