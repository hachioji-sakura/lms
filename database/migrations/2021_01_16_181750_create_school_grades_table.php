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
          $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('生徒ID');
          $table->string('title')->nullable(false)->comment('タイトル');
          $table->string('type')->nullable(false)->comment('成績タイプ');
          $table->string('remark',10000)->nullable(true)->comment('備考');
          $table->string('grade')->nullable(false)->comment('学年コード');
          $table->integer('semester_no')->default(1)->comment('学期番号');
          $table->string('s3_alias')->nullable(true)->default(null);
          $table->string('s3_url')->nullable(true)->default(null);
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
