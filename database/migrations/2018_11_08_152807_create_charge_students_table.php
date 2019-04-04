<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('生徒ID');
            $table->integer('teacher_id')->index('index_teacher_id')->comment('講師ID');
            $table->string('title')->default('')->comment('概要');
            $table->string('body')->default('')->comment('詳細');
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
        Schema::dropIfExists('charge_students');
    }
}
