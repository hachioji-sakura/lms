<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->string('status')->default('regular')->index('index_status')->after('user_id')->comment('ステータス/　trial=体験 / regular=入会 / recess=休会 / unsubscribe=退会');
          $table->date('recess_start_date')->nullable(true)->after('birth_day')->comment('休会開始日');
          $table->date('recess_end_date')->nullable(true)->after('recess_start_date')->comment('休会終了日');
          $table->date('unsubscribe_date')->nullable(true)->after('recess_end_date')->comment('退会日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->dropColumn('status');
          $table->dropColumn('recess_start_date');
          $table->dropColumn('recess_end_date');
          $table->dropColumn('unsubscribe_date');
        });
    }
}
