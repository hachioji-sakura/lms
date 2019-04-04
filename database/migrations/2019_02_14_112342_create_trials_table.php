<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_parent_id')->index('index_parent_id')->comment('保護者ID');
            $table->integer('student_id')->index('index_student_id')->comment('生徒ID');
            $table->string('status')->index('index_status')->default('new')->comment('新規登録:new / 確定:fix / キャンセル:cancel');
            $table->timestamp('trial_start_time1')->nullable(true)->comment('体験希望日時（第１希望）');
            $table->timestamp('trial_end_time1')->nullable(true)->comment('体験希望日時（第１希望）');
            $table->timestamp('trial_start_time2')->nullable(true)->comment('体験希望日時（第２希望）');
            $table->timestamp('trial_end_time2')->nullable(true)->comment('体験希望日時（第２希望）');
            $table->string('remark')->default('')->comment('備考');
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
        Schema::dropIfExists('trials');
    }
}
