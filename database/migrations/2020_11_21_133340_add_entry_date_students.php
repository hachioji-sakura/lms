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
          $table->date('entry_date')->nullable(true)->before('recess_start_date')->comment('入会日');
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
