<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->nullable(false)->index('index_teacher_id')->comment('担当講師');
            $table->string('title')->nullable(false)->default('生徒グループ')->comment('生徒グループ名');
            $table->string('remark')->default('')->comment('生徒グループ補足');
            $table->string('type')->nullable(false)->default('group')->comment('関係性:group or family');
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
        Schema::dropIfExists('student_groups');
    }
}
