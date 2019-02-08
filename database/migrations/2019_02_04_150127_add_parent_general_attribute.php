<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentGeneralAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_attributes', function (Blueprint $table) {
          $table->string('parent_attribute_key')->after('attribute_value')->nullable(true);
          $table->string('parent_attribute_value')->after('parent_attribute_key')->nullable(true);
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
          $table->dropColumn('parent_attribute_key');
          $table->dropColumn('parent_attribute_value');
        });
    }
}
