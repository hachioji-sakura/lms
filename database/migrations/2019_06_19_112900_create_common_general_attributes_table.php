<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonGeneralAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('general_attributes', function (Blueprint $table) {
          $table->increments('id');
          $table->string('attribute_key')->nullable(false)->index('index_attribute_key')->comment('属性キー');
          $table->string('attribute_value')->nullable(false)->index('index_attribute_value')->comment('属性値');
          $table->string('attribute_name')->comment('属性名');
          $table->integer('sort_no')->default(1)->comment('並び順');
          $table->string('parent_attribute_key')->nullable(true)->comment('親属性キー');;
          $table->string('parent_attribute_value')->nullable(true)->comment('親属性値');;
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
        Schema::connection('mysql_common')->dropIfExists('general_attributes');
    }
}
