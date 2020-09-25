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
          $table->integer('trial_id')->nullable(true)->comment('体験ID');
          $table->integer('entry_fee')->nullable(true)->comment('入会金');
          $table->integer('monthly_fee')->nullable(true)->comment('会費');
          $table->datetime('entry_date')->nullable(true)->comment('入会日');
          $table->string('status')->comment('ステータス')->index('index_status');
          $table->integer('student_parent_id')->comment('契約者ID');
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
