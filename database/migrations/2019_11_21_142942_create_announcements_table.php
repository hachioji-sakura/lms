<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false)->comment('件名');
            $table->string('body', 10000)->nullable(false)->comment('内容');
            $table->string('type')->nullable(false)->comment('コメント種別');
            $table->date('publiced_at')->comment('公開日');
            $table->integer('target_user_id')->index('index_target_user_id')->comment('対象ユーザーID');
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
        Schema::dropIfExists('announcements');
    }
}
