<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Manager;
class AddEntryDateManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('managers', function (Blueprint $table) {
          $table->date('entry_date')->after('birth_day')->nullable(true)->comment('入会日');
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
          $m = Manager::find(1)->first();
          if(isset($m->entry_date))  $table->dropColumn('entry_date');
        });
    }
}
