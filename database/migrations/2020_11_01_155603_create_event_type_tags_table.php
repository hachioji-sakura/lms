<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTypeTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_type_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_type_tag_id')->nullable(false)->index('index_event_type_tag_id')->comment('イベントタグID');
            $table->string('event_type_tag_key')->nullable(false)->index('index_event_type_tag_key')->comment('イベントタグキー');
            $table->string('event_type_tag_value')->comment('イベントタグ値');
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
        Schema::dropIfExists('event_type_tags');
    }
}
