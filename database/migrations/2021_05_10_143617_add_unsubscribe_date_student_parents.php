<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnsubscribeDateStudentParents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('student_parents', function (Blueprint $table) {
            $table->date('entry_date')->nullable(true)->after('birth_day')->comment('入会日');
            $table->date('unsubscribe_date')->nullable(true)->after('entry_date')->comment('退会日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('student_parents', function (Blueprint $table) {
            $table->dropColumn('entry_date');
            $table->dropColumn('unsubscribe_date');
        });
    }
}
