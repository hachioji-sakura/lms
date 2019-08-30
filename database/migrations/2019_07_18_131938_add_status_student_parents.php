<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusStudentParents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('student_parents', function (Blueprint $table) {
          $table->string('status')->default('regular')->index('index_status')->after('user_id')->comment('ステータス/　trial=体験 / regular=入会 / recess=休会 / unsubscribe=退会');
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
          $table->dropColumn('status');
        });
    }
}
