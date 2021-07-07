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
            $table->boolean('is_tax_include')->after('remark')->default(0)->comment('消費税計算タイプ');
            $table->double('consumption_tax_rate')->after('is_tax_include')->default(0.1)->comment('消費税率');
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
            $table->dropColumn('is_tax_include');
            $table->dropColumn('consumption_tax_rate');

        });
    }
}
