<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_grades', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('student_id')->nullable(false)->comment('生徒ID');
          $table->string('title')->nullable(false)->comment('タイトル');
          $table->string('remark',10000)->nullable(false)->comment('備考');
          $table->string('grade')->nullable(false)->comment('学年コード');
          $table->integer('semester_no')->nullable(false)->comment('学期番号');
          $table->string('s3_alias')->nullable()->default(null);
          $table->string('s3_url')->nullable()->default(null);
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
        Schema::dropIfExists('school_grades');
    }
}
