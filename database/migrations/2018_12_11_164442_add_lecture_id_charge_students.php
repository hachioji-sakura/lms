<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLectureIdChargeStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charge_students', function (Blueprint $table) {
          $table->integer('lecture_id')->default(0)->index('index_lecture_id')->after('teacher_id')->comment('レクチャーID');
          $table->dropColumn('title');
          $table->dropColumn('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charge_students', function (Blueprint $table) {
          $table->dropColumn('lecture_id');
        });
    }
}
