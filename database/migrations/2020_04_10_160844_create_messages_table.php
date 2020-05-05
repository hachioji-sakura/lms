<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_message_id')->nullable(false);
            $table->string('title')->nullable(false)->comment('件名');
            $table->string('body',10000)->nullable(false)->comment('内容');
            $table->string('type')->nullable(false);
            $table->string('s3_url')->nullable()->default(null);
            $table->string('s3_alias')->nullable()->default(null);
            $table->integer('target_user_id')->index('index_target_user_id');
            $table->integer('create_user_id')->index('index_create_user_id');
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
        Schema::dropIfExists('messages');
    }
}
