<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSchoolDepartmentsTable
 */
class CreateSchoolDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_type')->comment('学校タイプ');
            $table->integer('school_type_id')->unsigned()->comment('学校タイプに関連するID');
            $table->integer('department_id')->unsigned()->comment('学科ID');
            $table->timestamps();

            // インデックス
            $table->index(['school_type', 'school_type_id']);
            $table->unique( ['school_type', 'school_type_id', 'department_id'], 'school_departments_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_departments');
    }
}
