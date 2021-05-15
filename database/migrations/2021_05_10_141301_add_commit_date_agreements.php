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
            $table->datetime('commit_date')->after('entry_date')->nullable(true)->comment('承認日');
            $table->dropColumn('entry_date');
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
            $table->datetime('entry_date')->nullable(true)->comment('登録日');
        });
    }
}
