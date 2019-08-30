<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankinfoManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->string('bank_no')->default('')->comment('銀行番号')->after('address');
          $table->string('bank_branch_no')->default('')->comment('銀行支店番号')->after('bank_no');
          $table->string('bank_account_type')->default('')->comment('口座種別')->after('bank_branch_no');
          $table->string('bank_account_no')->default('')->comment('銀行口座番号')->after('bank_account_type');
          $table->string('bank_account_name')->default('')->comment('銀行口座名義')->after('bank_account_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->dropColumn('bank_no');
          $table->dropColumn('bank_branch_no');
          $table->dropColumn('bank_account_type');
          $table->dropColumn('bank_account_no');
          $table->dropColumn('bank_account_name');
        });
    }
}
