<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntryDateStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('students', function (Blueprint $table) {
          $table->date('entry_date')->after('birth_day')->nullable(true)->comment('入社日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('students', function (Blueprint $table) {
          $table->dropColumn('entry_date');
        });
    }
}
