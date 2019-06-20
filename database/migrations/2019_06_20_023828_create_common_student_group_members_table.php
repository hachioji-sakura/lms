<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonStudentGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('student_group_members', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('student_group_id')->index('index_student_group_id_id')->comment('生徒グループID');
          $table->integer('student_id')->nullable(false)->index('index_student_id')->comment('生徒ID');
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
        Schema::connection('mysql_common')->dropIfExists('student_group_members');
    }
}
