<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateHighSchoolDepartmentsTable
 */
class CreateHighSchoolDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('high_school_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('high_school_id')->unsigned()->comment('高等学校ID');
            $table->integer('department_id')->unsigned()->comment('学科ID');
            $table->timestamps();

            // インデックス
            $table->index('high_school_id');
            $table->index('department_id');
            $table->unique(['high_school_id','department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('high_school_departments');
    }
}
