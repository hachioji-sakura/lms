<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommitDateAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('agreements', function (Blueprint $table) {
            //
            $table->date('commit_date')->after('entry_date')->nullable(true)->comment('承認日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('agreements', function (Blueprint $table) {
            //
            $table->dropColumn('commit_date');
        });
    }
}
