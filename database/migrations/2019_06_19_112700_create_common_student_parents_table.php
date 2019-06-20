<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonStudentParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('student_parents', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id')->index('index_user_id')->comment('ユーザーID')->unique();
          $table->string('name_first')->nullable(false);
          $table->string('name_last')->nullable(false);
          $table->string('kana_first')->nullable(false);
          $table->string('kana_last')->nullable(false);
          $table->date('birth_day')->nullable(true)->default(null);
          $table->string('phone_no')->nullable(true);
          $table->string('address')->nullable(true);
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
          Schema::connection('mysql_common')->dropIfExists('student_parents');
    }
}
