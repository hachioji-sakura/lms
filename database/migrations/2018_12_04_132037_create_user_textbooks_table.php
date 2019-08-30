<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTextbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_textbooks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('index_user_id')->nullable(false)->comment('所有者ID');
            $table->integer('textbook_id')->index('index_textbook_id')->nullable(false)->comment('所有テキストＩＤ');
            $table->integer('status')->nullable(false)->default(0)->comment('所有状況');
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
        Schema::dropIfExists('user_textbooks');
    }
}
