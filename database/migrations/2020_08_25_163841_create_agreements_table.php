<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('agreements', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title')->nullable(true)->comment('概要');
          $table->string('type')->comment('タイプ');
          $table->integer('student_id')->comment('生徒ID');
          $table->integer('parent_agreement_id')->nullable(true)->comment('変更元ID');
          $table->integer('entry_fee')->nullable(true)->comment('入会金');
          $table->integer('monthly_fee')->nullable(true)->comment('会費');
          $table->datetime('entry_date')->nullable(true)->comment('登録日');
          $table->datetime('start_date')->nullable(true)->comment('開始日');
          $table->datetime('end_date')->nullable(true)->comment('終了日');
          $table->string('status')->comment('ステータス');
          $table->integer('student_parent_id')->comment('契約者ID')->index('student_parent_id_status');
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
        Schema::connection('mysql_common')->dropIfExists('agreements');
    }
}
