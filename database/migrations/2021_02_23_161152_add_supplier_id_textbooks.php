<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierIdTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('textbooks', function (Blueprint $table) {
        $table->integer('supplier_id')->after('publisher_id')->index('index_supplier_id')->nullable()->comment('発注先ID');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('textbooks', function (Blueprint $table) {
        $table->dropColumn('supplier_id');
      });
    }
}
