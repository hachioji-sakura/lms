<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetUserIdTextMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('text_materials', function (Blueprint $table) {
        $table->integer('target_user_id')->after('publiced_at')->index('index_add_target_user_id')->comment('対象者');
        $table->string('s3_alias')->before('s3_url')->nullable(true)->default('')->comment('ファイル名');

      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('text_materials', function (Blueprint $table) {
        $table->dropColumn('target_user_id');
        $table->dropColumn('s3_alias');
      });
    }
}
