<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusColumnsPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('places', function (Blueprint $table) {
            $table->string('status')->default("enabled")->after('name_en')->comment('ステータス');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('places', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
