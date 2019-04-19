<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrialStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('trial_students', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('trial_id')->nullable(false)->index('index_trial_id')->comment('カレンダーID');
          $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('対象生徒ID');
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
      Schema::dropIfExists('trial_students');
    }
}
