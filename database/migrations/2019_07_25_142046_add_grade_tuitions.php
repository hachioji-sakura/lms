<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGradeTuitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tuitions', function (Blueprint $table) {
          $table->string('grade')->default('')->after('course_type')->comment('学年');
          $table->string('subject')->after('course_type')->default('')->comment('科目');
          $table->integer('course_minutes')->after('course_type')->default(60)->comment('授業時間');
          $table->string('remark', 10000)->default('')->after('end_date')->comment('受講料　詳細');
          $table->dropColumn('kids_lesson');
          $table->dropColumn('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tuitions', function (Blueprint $table) {
          $table->dropColumn('grade');
          $table->dropColumn('subject');
          $table->dropColumn('course_minutes');
          $table->dropColumn('remark');
          $table->string('kids_lesson')->default('')->after('course_type')->comment('習い事：そろばん、ダンスなど');
          $table->string('body')->default('')->after('course_type')->comment('受講料　詳細');
        });
    }
}
