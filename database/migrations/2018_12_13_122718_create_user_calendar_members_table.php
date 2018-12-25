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
            $table->integer('calendar_id')->nullable(false)->index('index_calendar_id')->comment('ユーザーID');
            $table->integer('user_id')->nullable(false)->index('index_user_id')->comment('対象ユーザーID');
            $table->string('status')->default('new')->comment('登録:new / 承認:fix / キャンセル：cancel');
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
