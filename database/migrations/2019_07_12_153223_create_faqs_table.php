<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false)->comment('件名');
            $table->string('body')->nullable(false)->comment('内容');
            $table->string('type')->nullable(false)->comment('FAQ種別');
            $table->integer('sort_no')->default(1)->omment('並び順');
            $table->date('publiced_at')->nullable(true)->default(null)->comment('公開日');
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
        Schema::dropIfExists('faqs');
    }
}
