<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeachingTypeLessonRequestCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_request_calendars', function (Blueprint $table) {
          $table->string('teaching_type')->after('status')->default('')->comment('season=期間講習, trainng=演習');
          $table->integer('parent_lesson_request_calendar_id')->after('id')->default(0)->comment('親予定ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_request_calendars', function (Blueprint $table) {
          $table->dropColumn('teaching_type');
          $table->dropColumn('parent_lesson_request_calendar_id');
        });
    }
}
