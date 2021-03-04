<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subject_id')->comment('科目ID');
            $table->integer('average_point')->nullable(true)->comment('平均点');
            $table->integer('deviation')->nullable(true)->comment('偏差値');
            $table->integer('point')->comment('得点');
            $table->integer('max_point')->comment('満点');
            $table->date('taken_date')->nullable(true)->comment('実施日');
            $table->integer('exam_resultable_id')->comment('試験結果リレーションID');
            $table->string('exam_resultable_type')->comment('試験結果リレーションタイプ');
            $table->string('s3_url')->nullable(true)->comment('S3URL');
            $table->string('s3_alias')->nullable(true)->comment('S3エイリアス');
            $table->integer('create_user_id')->comment('起票者');
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
        Schema::dropIfExists('exam_results');
    }
}
