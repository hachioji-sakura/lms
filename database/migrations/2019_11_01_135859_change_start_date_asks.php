<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Ask;
class ChangeStartDateAsks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asks', function (Blueprint $table) {
          $table->string('from_time_slot')->after('status')->nullable(true)->comment('開始時分');
          $table->string('to_time_slot')->after('from_time_slot')->nullable(true)->comment('終了時分');
        });
      }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asks', function (Blueprint $table) {
          $table->dropColumn('from_time_slot');
          $table->dropColumn('to_time_slot');
        });
    }
}
