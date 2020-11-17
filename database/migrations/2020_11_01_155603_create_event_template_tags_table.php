<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTemplateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_template_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_template_id')->nullable(false)->index('index_event_template_id')->comment('イベントテンプレートID');
            $table->string('tag_key')->nullable(false)->index('index_event_template_tag_key')->comment('タグキー');
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
        Schema::dropIfExists('event_template_tags');
    }
}
