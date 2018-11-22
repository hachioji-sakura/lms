<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbooks', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name')->nullable(false);
          $table->string('explain')->default("")->comment('説明');
          $table->integer('selling_price')->default(0)->comment('販売価格');
          $table->integer('list_price')->default(0)->comment('定価');
          $table->integer('price1')->default(0);
          $table->integer('price2')->default(0);
          $table->integer('price3')->default(0);
          $table->string('url')->default('')->comment('販売元ページ');
          $table->integer('publisher_id')->index('index_publisher_id')->comment('出版社ID');
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
        Schema::dropIfExists('textbooks');
    }
}
