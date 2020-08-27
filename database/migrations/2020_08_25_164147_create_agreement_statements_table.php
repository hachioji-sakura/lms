<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreement_statements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_price')->nullable(true)->comment('単価');
            $table->integer('teacher_id')->nullable(true)->comment('講師ID');
            $table->integer('agreement_id')->nullable(true)->comment('契約ID')->index('index_agreement_id');
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
        Schema::connection('mysql_common')->dropIfExists('agreement_statements');
    }
}
