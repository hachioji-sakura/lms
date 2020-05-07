<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class AddLocaleUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->table('users', function (Blueprint $table) {
            //
            $table->string('locale')->after('status')->default('ja');
        });
        User::whereIn('id', function($query){
          $query->select('user_id')
                ->from('common.teachers')
                ->where('name_first','regexp','[a-z]');
        })->update(['locale' => 'en']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('common.users', function (Blueprint $table) {
            //
            $table->dropColumn('locale');
        });
    }
}
