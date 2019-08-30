<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('mails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_address')->nullable(false)->comment('From');
            $table->string('to_address')->nullable(false)->comment('To');
            $table->string('subject')->nullable(false)->default('')->comment('件名');
            $table->string('body')->default('')->comment('内容');
            $table->string('template')->default('')->comment('使用テンプレート');
            $table->string('type')->default('text')->comment('text or html or slack');
            $table->string('locale')->default('ja')->comment('使用言語');
            $table->string('status')->nullable(true)->comment('送信ステータス');
            $table->timestamp('send_schedule')->nullable(true)->comment('送信時間（予定）');
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
        Schema::connection('mysql_common')->dropIfExists('mails');
    }
}
