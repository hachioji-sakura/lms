<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attribute_key')->nullable(false)->index('index_attribute_key')->comment('属性キー');
            $table->string('attribute_value')->nullable(false)->index('index_attribute_value')->comment('属性値');
            $table->string('attribute_name')->comment('属性名');
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
        Schema::dropIfExists('attributes');
    }
}
