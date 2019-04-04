<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_relations', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('生徒ID');
          $table->integer('student_parent_id')->index('index_student_parent_id')->comment('保護者ID');
          $table->string('type')->default('family')->comment('関係性');
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
        Schema::dropIfExists('student_relations');
    }
}
