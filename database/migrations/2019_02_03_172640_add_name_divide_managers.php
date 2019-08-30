<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameDivideManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managers', function (Blueprint $table) {
          $table->string('name_first')->nullable(false)->after('name');
          $table->string('kana_first')->nullable(false)->after('kana');
          $table->integer('gender')->nullable(false)->after('kana_first')->default(0);
          $table->date('birth_day')->nullable(true)->after('gender')->default(null);
          $table->string('phone_no')->nullable(true)->after('birth_day');
          $table->renameColumn('name', 'name_last');
          $table->renameColumn('kana', 'kana_last');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('managers', function (Blueprint $table) {
          $table->renameColumn('kana_last', 'kana');
          $table->renameColumn('name_last', 'name');
          $table->dropColumn('kana_first');
          $table->dropColumn('name_first');
          $table->dropColumn('gender');
          $table->dropColumn('birth_day');
          $table->dropColumn('phone_no');
        });
    }
}
