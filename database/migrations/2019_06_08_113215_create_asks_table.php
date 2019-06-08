<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asks', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title')->nullable(false)->comment('件名');
          $table->string('body')->nullable(false)->comment('内容');
          $table->string('type')->nullable(false)->default('')->comment('種別');
          $table->string('status')->nullable(false)->default('new')->comment('ステータス');
          $table->date('start_date')->nullable(true)->comment('開始日');
          $table->date('end_date')->nullable(true)->comment('終了日');
          $table->string('target_model')->default('')->comment('更新対象model');
          $table->integer('target_id')->default(0)->comment('更新対象model.id');
          $table->integer('charge_user_id')->index('index_charge_user_id')->comment('担当ユーザーID');
          $table->integer('target_user_id')->index('index_target_user_id')->comment('対象ユーザーID');
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
        Schema::dropIfExists('asks');
    }
}
