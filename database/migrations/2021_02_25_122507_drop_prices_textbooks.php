<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPricesTextbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('textbooks', function (Blueprint $table) {
        $table->dropColumn('selling_price');
        $table->dropColumn('list_price');
        $table->dropColumn('price1');
        $table->dropColumn('price2');
        $table->dropColumn('price3');
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
        $table->integer('selling_price')->default(0)->comment('販売価格');
        $table->integer('list_price')->default(0)->comment('定価');
        $table->integer('price1')->default(0);
        $table->integer('price2')->default(0);
        $table->integer('price3')->default(0);
      });
    }
}
