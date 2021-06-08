<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddS3urlExams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('s3_url')->nullable(true)->after('remark')->comment('S3URL');
            $table->string('s3_alias')->nullable(true)->after('s3_url')->comment('S3エイリアス');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('s3_url');
            $table->dropColumn('s3_alias');
          });
    }
}
