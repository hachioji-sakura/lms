<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false)->comment('件名');
            $table->string('url')->nullable(true)->comment('URL');
            $table->string('body',10000)->nullable(true)->comment('内容');
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
        Schema::dropIfExists('event_templates');
    }
}
