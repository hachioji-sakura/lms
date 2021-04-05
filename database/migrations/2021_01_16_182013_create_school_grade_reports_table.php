<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolGradeReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_grade_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_grade_id')->nullable(false)->index('index_school_grade_id')->comment('成績ID');
            $table->integer('subject_id')->nullable(false)->index('index_subject_id')->comment('科目ID');
            $table->integer('report_point')->default(1)->comment('評点');
            $table->string('remark',10000)->nullable(true)->comment('備考');
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
        Schema::dropIfExists('school_grade_reports');
    }
}
