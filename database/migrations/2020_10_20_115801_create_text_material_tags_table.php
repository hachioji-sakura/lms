<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextMaterialTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_material_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('text_material_id')->nullable(false)->index('index_text_material_id')->comment('text_material_id');
            $table->string('tag_key')->nullable(false)->index('index_tag_key')->comment('タグキー');
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
        Schema::dropIfExists('text_material_tags');
    }
}
