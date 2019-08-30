<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaceFloorSheatIdUserCalendarMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->integer('place_floor_sheat_id')->default(0)->index('index_place_floor_sheat_id')->comment('座席ID')->after("status");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_members', function (Blueprint $table) {
          $table->dropColumn('place_floor_sheat_id');
        });
    }
}
