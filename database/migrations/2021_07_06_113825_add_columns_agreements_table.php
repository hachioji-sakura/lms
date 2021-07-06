<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAgreementsTable extends Migration
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
            $table->string('consumption_tax_type')->after('remark')->default("exclude")->comment('消費税計算タイプ');
            $table->double('consumption_tax_rate')->after('consumption_tax_type')->default(0.1)->comment('消費税率');
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
            $table->dropColumn('consumption_tax_type');
            $table->dropColumn('consumption_tax_rate');

        });
    }
}
