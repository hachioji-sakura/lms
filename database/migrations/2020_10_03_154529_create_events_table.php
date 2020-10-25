<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_type_id')->comment('イベント種別ID');
            $table->string('title')->nullable(false)->comment('件名');
            $table->date('event_from_date')->nullable(false)->comment('開催期間_始');
            $table->date('event_to_date')->nullable(false)->comment('開催期間_終');
            $table->date('response_from_date')->nullable(false)->comment('回答期間_始');
            $table->date('response_to_date')->nullable(false)->comment('回答期間_終');
            $table->string('body',10000)->nullable(true)->comment('備考');
            $table->integer('status')->nullable(false)->comment('ステータス'); //後から追加したフィールド
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
        Schema::dropIfExists('events');
    }
}
