<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBankInfoManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->string('bank_no')->nullable(true)->change();
          $table->string('bank_branch_no')->nullable(true)->change();
          $table->string('bank_account_type')->nullable(true)->change();
          $table->string('bank_account_no')->nullable(true)->change();
          $table->string('bank_account_name')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('managers', function (Blueprint $table) {
            //
        });
    }
}
