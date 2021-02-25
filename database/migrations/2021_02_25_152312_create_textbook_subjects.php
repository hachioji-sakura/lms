<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookSubjects extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('textbook_subjects', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('subject_id')->index('index_subject_id')->comment('教科ID');
      $table->integer('textbook_id')->index('index_event_id')->comment('教材ID');
//        $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
//        $table->foreign('textbook_id')->references('textbook_id')->on('textbooks')->onDelete('cascade');
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
    Schema::dropIfExists('textbook_subjects');
  }
}
