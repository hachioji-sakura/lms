<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbook_chapters', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('textbook_id')->index('index_textbook_id')->nullable(false)->comment('教科書マスタのID');
          $table->integer('sort_no')->nullable(false)->default(0)->comment('順番');
          $table->string('title')->nullable(false)->comment('章タイトル');
          $table->string('body')->default('')->comment('章説明');
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
        Schema::dropIfExists('textbook_chapters');
    }
}
