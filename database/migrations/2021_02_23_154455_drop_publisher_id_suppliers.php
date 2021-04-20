<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPublisherIdSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('suppliers', function (Blueprint $table) {
      $table->dropColumn('publisher_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('suppliers', function (Blueprint $table) {
      $table->integer('publisher_id')->comment('出版社ID');
    });
  }

}
