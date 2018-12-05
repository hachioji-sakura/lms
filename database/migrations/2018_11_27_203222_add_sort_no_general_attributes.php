<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortNoGeneralAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_attributes', function (Blueprint $table) {
          $table->integer('sort_no')->default(1)->after('attribute_name')->comment('並び順');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_attributes', function (Blueprint $table) {
          $table->dropColumn('sort_no');
        });
    }
}
