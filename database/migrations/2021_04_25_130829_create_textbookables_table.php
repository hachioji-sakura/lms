<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookablesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('textbookables', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('textbook_id')->nullable(false)->index('index_textbook_id')->comment('テキストID');
      $table->string('textbookable_type')->nullable(false)->comment('親テーブル名');
      $table->integer('textbookable_id')->nullable(false)->index('index_textbookable_id')->comment('親テーブルID');
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
    Schema::dropIfExists('textbookable');
  }
}
