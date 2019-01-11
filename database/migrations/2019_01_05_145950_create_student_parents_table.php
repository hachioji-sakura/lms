<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_parents', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id')->index('index_user_id')->comment('ユーザーID')->unique();
          $table->string('name_first')->nullable(false);
          $table->string('name_last')->nullable(false);
          $table->string('kana_first')->nullable(false);
          $table->string('kana_last')->nullable(false);
          $table->integer('create_user_id');
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
        Schema::dropIfExists('student_parents');
    }
}
